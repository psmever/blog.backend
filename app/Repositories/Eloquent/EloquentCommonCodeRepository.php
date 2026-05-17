<?php

namespace App\Repositories\Eloquent;

use App\Models\CommonCode;
use App\Repositories\CommonCodeRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentCommonCodeRepository implements CommonCodeRepositoryInterface
{
    public function getActiveOrdered(array $columns = ['*']): Collection
    {
        return CommonCode::query()
            ->active()
            ->orderBy('group_key')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get($columns);
    }

    public function findActiveByGroupAndCode(
        string $groupKey,
        string $code,
        array $columns = ['*']
    ): ?CommonCode {
        return CommonCode::query()
            ->forGroup($groupKey)
            ->active()
            ->where('code', $code)
            ->first($columns);
    }
}
