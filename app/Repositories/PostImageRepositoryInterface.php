<?php

namespace App\Repositories;

use App\Models\PostImage;

interface PostImageRepositoryInterface
{
    public function create(array $attributes): PostImage;

    public function findByUrlForPostUuidAndUser(string $postUuid, int $userId, string $url): ?PostImage;

    public function attachStagedImagesToPost(string $postUuid, int $userId, int $postId): void;
}
