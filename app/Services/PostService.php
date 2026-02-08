<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\TagRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly TagRepositoryInterface $tags
    ) {}

    public function create(User $user, array $payload): Post
    {
        return DB::transaction(function () use ($user, $payload) {
            $tagNames = collect($payload['tags'] ?? [])
                ->filter(fn ($tag) => is_string($tag))
                ->map(fn ($tag) => trim($tag))
                ->filter(fn ($tag) => $tag !== '')
                ->unique()
                ->values()
                ->all();

            $slug = $this->makeUniqueSlug($user->getKey(), $payload['title']);

            $post = $this->posts->create([
                'user_id' => $user->getKey(),
                'title' => $payload['title'],
                'slug' => $slug,
                'body' => $payload['body'],
            ]);

            $tagModels = $this->tags->findOrCreateByNames($tagNames);
            if ($tagModels->isNotEmpty()) {
                $post->tags()->sync($tagModels->pluck('id')->unique()->all());
            }
            $post->load('tags');

            return $post;
        });
    }

    private function makeUniqueSlug(int $userId, string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'post';
        }

        $slug = $base;
        $suffix = 2;

        while ($this->posts->slugExistsForUser($userId, $slug)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
