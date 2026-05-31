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

    public function slugExists(string $slug): bool
    {
        return Post::query()
            ->where('slug', $slug)
            ->exists();
    }

    public function slugExistsExceptPost(string $slug, int $postId): bool
    {
        return Post::query()
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
            ->with(['coverImage.thumbnailVariant', 'tags'])
            ->where('user_id', $userId)
            ->where('uuid', $uuid)
            ->first();
    }

    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::query()
            ->with(['coverImage.thumbnailVariant', 'tags', 'user'])
            ->where('slug', $slug)
            ->where('status', Post::STATUS_PUBLISHED)
            ->first();
    }

    public function incrementViewCount(Post $post): void
    {
        Post::query()
            ->whereKey($post->getKey())
            ->increment('view_count');
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

    /**
     * @return Collection<int, Post>
     */
    public function listPublishedBeforeCursor(?string $publishedAt, ?int $id, int $limit): Collection
    {
        $query = Post::query()
            ->with(['coverImage.thumbnailVariant', 'tags', 'user'])
            ->where('status', Post::STATUS_PUBLISHED);

        if ($publishedAt !== null && $id !== null) {
            $query->where(function ($builder) use ($publishedAt, $id) {
                $builder->where('published_at', '<', $publishedAt)
                    ->orWhere(function ($nested) use ($publishedAt, $id) {
                        $nested->where('published_at', $publishedAt)
                            ->where('id', '<', $id);
                    });
            });
        }

        return $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
