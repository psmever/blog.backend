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

    public function findByUuidForPostAndUser(int $postId, int $userId, string $uuid): ?PostImage
    {
        return PostImage::query()
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->where('uuid', $uuid)
            ->first();
    }

    public function findByUuidForPostUuidAndUser(string $postUuid, int $userId, string $uuid): ?PostImage
    {
        return PostImage::query()
            ->where('post_uuid', $postUuid)
            ->where('user_id', $userId)
            ->where('uuid', $uuid)
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
}
