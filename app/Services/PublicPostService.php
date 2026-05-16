<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicPostService
{
    public function __construct(
        private readonly PostRepositoryInterface $posts
    ) {}

    /**
     * @return array{0: Collection<int, Post>, 1: ?string, 2: bool}
     */
    public function listPublished(int $limit, ?string $cursor): array
    {
        [$publishedAt, $id] = $this->decodeCursor($cursor);

        $posts = $this->posts->listPublishedBeforeCursor(
            $publishedAt,
            $id,
            $limit + 1
        );

        $hasMore = $posts->count() > $limit;
        if ($hasMore) {
            $posts = $posts->take($limit)->values();
        }

        $lastPost = $posts->last();
        $nextCursor = $hasMore && $lastPost instanceof Post
            ? $this->encodeCursor($lastPost)
            : null;

        return [$posts, $nextCursor, $hasMore];
    }

    public function findPublishedBySlug(string $slug, Request $request): ?Post
    {
        $post = $this->posts->findPublishedBySlug($this->normalizeSlug($slug));
        if (! $post) {
            return null;
        }

        $sessionKey = sprintf('public_post_viewed:%d', $post->getKey());

        if (! $request->session()->has($sessionKey)) {
            $this->posts->incrementViewCount($post);
            $request->session()->put($sessionKey, true);
            $post->refresh();
            $post->loadMissing(['coverImage', 'tags', 'user']);
        }

        return $post;
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

    public function excerptFromBody(?string $body): string
    {
        $text = (string) $body;
        if ($text === '') {
            return '';
        }

        $text = preg_replace('/!\[[^\]]*]\((.*?)\)/u', ' ', $text) ?? $text;
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/u', '$1', $text) ?? $text;
        $text = preg_replace('/[`*_>#-]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/<[^>]+>/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($text === '') {
            return '';
        }

        return Str::limit($text, 160, '...');
    }

    /**
     * @return array{0: ?string, 1: ?int}
     */
    private function decodeCursor(?string $cursor): array
    {
        if ($cursor === null || trim($cursor) === '') {
            return [null, null];
        }

        $normalized = strtr($cursor, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);
        if (! is_string($decoded) || $decoded === '') {
            throw new ApiException('cursor 값이 올바르지 않습니다.', 422, [
                'cursor' => ['cursor 값이 올바르지 않습니다.'],
            ]);
        }

        $payload = json_decode($decoded, true);
        if (
            ! is_array($payload)
            || ! isset($payload['published_at'], $payload['id'])
            || ! is_string($payload['published_at'])
            || ! is_numeric($payload['id'])
        ) {
            throw new ApiException('cursor 값이 올바르지 않습니다.', 422, [
                'cursor' => ['cursor 값이 올바르지 않습니다.'],
            ]);
        }

        return [$payload['published_at'], (int) $payload['id']];
    }

    private function encodeCursor(Post $post): string
    {
        $payload = json_encode([
            'published_at' => $post->published_at?->toDateTimeString(),
            'id' => (int) $post->getKey(),
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }
}
