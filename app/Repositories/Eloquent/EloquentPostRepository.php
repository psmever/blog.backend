<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\PostRepositoryInterface;

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

    public function findByUuidForUser(int $userId, string $uuid): ?Post
    {
        return Post::query()
            ->with('tags')
            ->where('user_id', $userId)
            ->where('uuid', $uuid)
            ->first();
    }
}
