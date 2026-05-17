<?php

namespace App\Repositories\Eloquent;

use App\Models\PostImage;
use App\Repositories\PostImageRepositoryInterface;

class EloquentPostImageRepository implements PostImageRepositoryInterface
{
    public function create(array $attributes): PostImage
    {
        return PostImage::query()->create($attributes);
    }

    public function findByUrlForPostUuidAndUser(string $postUuid, int $userId, string $url): ?PostImage
    {
        $normalizedUrl = $this->normalizeUrlPath($url);
        $storagePath = $this->storagePathFromUrl($normalizedUrl);

        return PostImage::query()
            ->where('post_uuid', $postUuid)
            ->where('user_id', $userId)
            ->where(function ($query) use ($url, $normalizedUrl, $storagePath) {
                $query->where('url', $url)
                    ->orWhere('url', $normalizedUrl);

                if ($storagePath !== null) {
                    $query->orWhere('path', $storagePath);
                }
            })
            ->first();
    }

    public function attachStagedImagesToPost(string $postUuid, int $userId, int $postId): void
    {
        PostImage::query()
            ->where('post_uuid', $postUuid)
            ->where('user_id', $userId)
            ->whereNull('post_id')
            ->update(['post_id' => $postId]);
    }

    private function normalizeUrlPath(string $url): string
    {
        $urlPath = parse_url($url, PHP_URL_PATH);
        $urlQuery = parse_url($url, PHP_URL_QUERY);
        $urlFragment = parse_url($url, PHP_URL_FRAGMENT);

        if (! is_string($urlPath) || $urlPath === '') {
            $urlPath = '/'.ltrim($url, '/');
        } elseif (! str_starts_with($urlPath, '/')) {
            $urlPath = '/'.$urlPath;
        }

        return $urlPath
            .(is_string($urlQuery) && $urlQuery !== '' ? '?'.$urlQuery : '')
            .(is_string($urlFragment) && $urlFragment !== '' ? '#'.$urlFragment : '');
    }

    private function storagePathFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return null;
        }

        if (str_starts_with($path, '/storage/')) {
            return ltrim(substr($path, strlen('/storage/')), '/');
        }

        return ltrim($path, '/');
    }
}
