<?php

namespace App\Repositories;

use App\Models\CommonCode;
use Illuminate\Support\Collection;

interface CommonCodeRepositoryInterface
{
    /**
     * 활성화된 공통 코드를 정렬하여 반환한다.
     */
    public function getActiveOrdered(array $columns = ['*']): Collection;

    /**
     * 그룹/코드 기준으로 활성화된 공통 코드 1건을 조회한다.
     */
    public function findActiveByGroupAndCode(
        string $groupKey,
        string $code,
        array $columns = ['*']
    ): ?CommonCode;
}
