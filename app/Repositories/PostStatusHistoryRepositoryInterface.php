<?php

namespace App\Repositories;

use App\Models\PostStatusHistory;

interface PostStatusHistoryRepositoryInterface
{
    public function create(array $attributes): PostStatusHistory;
}
