<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use App\Support\PostImageResponseFormatter;

class PageMetaService
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly PublicPostService $publicPosts,
        private readonly ShortUrlService $shortUrls,
        private readonly PostImageResponseFormatter $postImageFormatter
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getMeta(string $url): array
    {
        $requestedUrl = $this->normalizeUrl($url);
        $resolvedUrl = $requestedUrl;
        $isShortUrl = false;

        $shortUrl = $this->shortUrls->resolveShortUrl($requestedUrl);
        if ($shortUrl) {
            $resolvedUrl = (string) $shortUrl->original_url;
            $isShortUrl = true;
        }

        $post = $this->findPostByUrl($resolvedUrl);
        if (! $post) {
            throw new ApiException('메타데이터를 찾을 수 없습니다.', 404);
        }

        $coverImage = $this->postImageFormatter->format($post->coverImage);
        $imageUrl = (string) ($coverImage['url'] ?? '');

        return [
            'url' => $requestedUrl,
            'resolved_url' => $resolvedUrl,
            'canonical_url' => $resolvedUrl,
            'title' => (string) $post->title,
            'description' => $this->publicPosts->excerptFromBody($post->body),
            'image_url' => $this->withoutDomain($imageUrl),
            'type' => 'article',
            'site_name' => (string) config('app.name'),
            'locale' => 'ko_KR',
            'published_time' => $this->formatIsoDateTime($post->published_at),
            'modified_time' => $this->formatIsoDateTime($post->updated_at),
            'robots' => [
                'index' => ! $isShortUrl,
                'follow' => true,
            ],
        ];
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);

        if (
            $url === ''
            || ! str_starts_with($url, '/')
            || str_starts_with($url, '//')
            || preg_match('/^[a-z][a-z0-9+\-.]*:/i', $url) === 1
        ) {
            throw new ApiException('url 값이 올바르지 않습니다.', 422, [
                'url' => ['프런트 내부 경로만 사용할 수 있습니다.'],
            ]);
        }

        return $url;
    }

    private function findPostByUrl(string $url): ?Post
    {
        if (! str_starts_with($url, '/posts/')) {
            return null;
        }

        $slug = substr($url, strlen('/posts/'));
        if ($slug === '' || str_contains($slug, '/')) {
            return null;
        }

        return $this->posts->findPublishedBySlug($this->normalizeSlug($slug));
    }

    private function normalizeSlug(string $slug): string
    {
        $normalized = $slug;

        for ($attempts = 0; $attempts < 2; $attempts++) {
            $decoded = rawurldecode($normalized);
            if ($decoded === $normalized) {
                break;
            }

            $normalized = $decoded;
        }

        return $normalized;
    }

    private function withoutDomain(string $url): string
    {
        if ($url === '') {
            return '';
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return $url;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        return $path
            .(is_string($query) && $query !== '' ? '?'.$query : '')
            .(is_string($fragment) && $fragment !== '' ? '#'.$fragment : '');
    }

    private function formatIsoDateTime($dateTime): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        return $dateTime->copy()
            ->setTimezone(config('app.display_timezone', 'Asia/Seoul'))
            ->toIso8601String();
    }
}
