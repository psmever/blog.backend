<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentPostRepository implements PostRepositoryInterface
{
    public function create(array $attributes): Post
    {
        return Post::query()->create($attributes);
    }

    public function update(Post $post, array $attributes): Post
    {
        $post->fill($attributes);
        $post->save();

        return $post->refresh();
    }

    public function slugExistsForUser(int $userId, string $slug): bool
    {
        return Post::query()
            ->where('user_id', $userId)
            ->where('slug', $slug)
            ->exists();
    }

    public function slugExistsForUserExceptPost(int $userId, string $slug, int $postId): bool
    {
        return Post::query()
            ->where('user_id', $userId)
            ->where('slug', $slug)
            ->whereKeyNot($postId)
            ->exists();
    }

    public function uuidExists(string $uuid): bool
    {
        return Post::query()
            ->where('uuid', $uuid)
            ->exists();
    }

    public function findByUuidForUser(int $userId, string $uuid): ?Post
    {
        return Post::query()
            ->with(['coverImage', 'tags'])
            ->where('user_id', $userId)
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * @return Collection<int, Post>
     */
    public function listForUserByStatus(int $userId, string $status, int $limit): Collection
    {
        return Post::query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }
}
