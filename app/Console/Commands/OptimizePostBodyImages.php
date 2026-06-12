<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostImageVariant;
use App\Repositories\PostImageRepositoryInterface;
use App\Services\PostImageService;
use App\Services\PostImageThumbnailService;
use Illuminate\Console\Command;
use Throwable;

class OptimizePostBodyImages extends Command
{
    protected $signature = 'posts:optimize-body-images
        {--dry-run : Preview changes without writing files or updating posts}
        {--chunk=100 : Number of images to process per chunk}';

    protected $description = 'Regenerate resized body images and rewrite post body image URLs';

    public function handle(
        PostImageThumbnailService $images,
        PostImageService $imageUrls,
        PostImageRepositoryInterface $postImages
    ): int {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = max(1, (int) $this->option('chunk'));
        $processed = 0;
        $optimized = 0;
        $rewrittenPosts = 0;
        $rewrittenUrls = 0;
        $syncedCovers = 0;
        $failed = 0;
        $processedPostUuids = [];

        try {
            $images->ensureSupported();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        PostImage::query()
            ->with('bodyVariant')
            ->orderBy('id')
            ->chunkById($chunk, function ($postImages) use (
                $images,
                $imageUrls,
                $dryRun,
                &$processed,
                &$optimized,
                &$rewrittenPosts,
                &$rewrittenUrls,
                &$processedPostUuids,
                &$failed
            ): void {
                foreach ($postImages as $postImage) {
                    $processed++;

                    try {
                        $bodyUrl = $dryRun
                            ? $this->bodyImageUrlForDryRun($postImage, $imageUrls)
                            : $imageUrls->urlForVariant($images->createBodyForImage($postImage, force: true));

                        $optimized++;

                        [$postUpdated, $urlCount] = $this->rewritePostBody($postImage, $bodyUrl, $imageUrls, $dryRun);
                        if ($postUpdated) {
                            $rewrittenPosts++;
                            $rewrittenUrls += $urlCount;
                        }

                        if ($postImage->post_uuid !== null) {
                            $processedPostUuids[(string) $postImage->post_uuid] = true;
                        }
                    } catch (Throwable $e) {
                        $failed++;
                        $this->error(sprintf('%s: %s', $postImage->path, $e->getMessage()));
                    }
                }
            });

        foreach (array_keys($processedPostUuids) as $postUuid) {
            try {
                if ($this->syncCoverImage($postUuid, $postImages, $dryRun)) {
                    $syncedCovers++;
                }
            } catch (Throwable $e) {
                $failed++;
                $this->error(sprintf('%s: %s', $postUuid, $e->getMessage()));
            }
        }

        $this->info(sprintf(
            'Post body image optimization %s: processed=%d, optimized=%d, rewritten_posts=%d, rewritten_urls=%d, synced_covers=%d, failed=%d',
            $dryRun ? 'dry-run completed' : 'completed',
            $processed,
            $optimized,
            $rewrittenPosts,
            $rewrittenUrls,
            $syncedCovers,
            $failed
        ));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{0: bool, 1: int}
     */
    private function rewritePostBody(PostImage $image, string $bodyUrl, PostImageService $imageUrls, bool $dryRun): array
    {
        $post = Post::query()
            ->where('uuid', $image->post_uuid)
            ->first();

        if (! $post || (string) $post->body === '') {
            return [false, 0];
        }

        [$body, $count] = $this->replaceMarkdownImageUrls(
            (string) $post->body,
            $this->replacementMap($image, $bodyUrl, $imageUrls)
        );

        if ($count === 0 || $body === (string) $post->body) {
            return [false, 0];
        }

        if (! $dryRun) {
            $post->forceFill(['body' => $body])->save();
        }

        return [true, $count];
    }

    private function syncCoverImage(string $postUuid, PostImageRepositoryInterface $postImages, bool $dryRun): bool
    {
        $post = Post::query()
            ->where('uuid', $postUuid)
            ->first();

        if (! $post) {
            return false;
        }

        $coverImageId = null;
        foreach ($this->extractImageUrlsFromBody((string) $post->body) as $url) {
            $image = $postImages->findByUrlForPostUuidAndUser(
                $postUuid,
                (int) $post->user_id,
                $url
            );

            if (! $image) {
                continue;
            }

            $coverImageId = (int) $image->getKey();
            break;
        }

        if ($post->cover_image_id === $coverImageId) {
            return false;
        }

        if (! $dryRun) {
            $post->forceFill(['cover_image_id' => $coverImageId])->save();
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    private function extractImageUrlsFromBody(string $body): array
    {
        if ($body === '') {
            return [];
        }

        preg_match_all('/!\[[^\]]*]\((<[^>]+>|[^)\s]+)(?:\s+"[^"]*")?\)/u', $body, $matches);
        /** @var array{0: list<string>, 1: list<string>} $matches */

        return array_values(array_filter(
            array_map(
                static fn (string $url): string => trim($url, '<>'),
                $matches[1]
            ),
            static fn (string $url): bool => $url !== ''
        ));
    }

    /**
     * @param  array<string, string>  $replacements
     * @return array{0: string, 1: int}
     */
    private function replaceMarkdownImageUrls(string $body, array $replacements): array
    {
        $count = 0;
        $rewritten = preg_replace_callback(
            '/!\[[^\]]*]\((<[^>]+>|[^)\s]+)(\s+"[^"]*")?\)/u',
            function (array $matches) use ($replacements, &$count): string {
                $rawUrl = $matches[1];
                $url = trim($rawUrl, '<>');
                $replacement = $replacements[$url] ?? null;

                if ($replacement === null) {
                    return $matches[0];
                }

                $count++;
                $wrappedUrl = str_starts_with($rawUrl, '<') && str_ends_with($rawUrl, '>')
                    ? '<'.$replacement.'>'
                    : $replacement;

                return str_replace($rawUrl, $wrappedUrl, $matches[0]);
            },
            $body
        );

        return [is_string($rewritten) ? $rewritten : $body, $count];
    }

    /**
     * @return array<string, string>
     */
    private function replacementMap(PostImage $image, string $bodyUrl, PostImageService $imageUrls): array
    {
        $urls = array_filter([
            (string) $image->url,
            $this->normalizeUrlPath((string) $image->url),
            $imageUrls->urlForImage($image),
        ]);

        $map = [];
        foreach (array_unique($urls) as $url) {
            if ($url !== $bodyUrl) {
                $map[$url] = $bodyUrl;
            }
        }

        return $map;
    }

    private function bodyImageUrlForDryRun(PostImage $image, PostImageService $imageUrls): string
    {
        $variant = new PostImageVariant([
            'disk' => $image->disk,
            'path' => sprintf('posts/%s/body-resized/%s.webp', $image->post_uuid, $image->uuid),
        ]);

        return $imageUrls->urlForVariant($variant);
    }

    private function normalizeUrlPath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        if (! is_string($path) || $path === '') {
            return '';
        }

        return '/'.ltrim($path, '/')
            .(is_string($query) && $query !== '' ? '?'.$query : '')
            .(is_string($fragment) && $fragment !== '' ? '#'.$fragment : '');
    }
}
