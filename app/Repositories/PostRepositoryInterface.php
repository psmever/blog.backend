<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepositoryInterface
{
    public function create(array $attributes): Post;

    public function slugExistsForUser(int $userId, string $slug): bool;
}
