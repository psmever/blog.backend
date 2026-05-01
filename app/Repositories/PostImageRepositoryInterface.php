<?php

namespace App\Repositories;

use App\Models\PostImage;

interface PostImageRepositoryInterface
{
    public function create(array $attributes): PostImage;

    public function findByUuidForPostAndUser(int $postId, int $userId, string $uuid): ?PostImage;

    public function findByUuidForPostUuidAndUser(string $postUuid, int $userId, string $uuid): ?PostImage;

    public function attachStagedImagesToPost(string $postUuid, int $userId, int $postId): void;
}
