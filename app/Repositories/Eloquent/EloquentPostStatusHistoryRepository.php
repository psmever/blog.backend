<?php

namespace App\Repositories\Eloquent;

use App\Models\PostStatusHistory;
use App\Repositories\PostStatusHistoryRepositoryInterface;

class EloquentPostStatusHistoryRepository implements PostStatusHistoryRepositoryInterface
{
    public function create(array $attributes): PostStatusHistory
    {
        return PostStatusHistory::query()->create($attributes);
    }
}
