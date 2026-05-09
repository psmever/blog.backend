<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    public function create(array $attributes): Post;

    public function update(Post $post, array $attributes): Post;

    public function slugExists(string $slug): bool;

    public function slugExistsExceptPost(string $slug, int $postId): bool;

    public function uuidExists(string $uuid): bool;

    public function findByUuidForUser(int $userId, string $uuid): ?Post;

    public function findPublishedBySlug(string $slug): ?Post;

    public function incrementViewCount(Post $post): void;

    /**
     * @return Collection<int, Post>
     */
    public function listForUserByStatus(int $userId, string $status, int $limit): Collection;

    /**
     * @return Collection<int, Post>
     */
    public function listPublishedBeforeCursor(?string $publishedAt, ?int $id, int $limit): Collection;
}
