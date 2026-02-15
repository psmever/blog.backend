<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface
{
    public function create(array $attributes): Post;

    public function update(Post $post, array $attributes): Post;

    public function slugExistsForUser(int $userId, string $slug): bool;

    public function slugExistsForUserExceptPost(int $userId, string $slug, int $postId): bool;

    public function findByUuidForUser(int $userId, string $uuid): ?Post;
}
