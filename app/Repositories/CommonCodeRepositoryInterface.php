<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface CommonCodeRepositoryInterface
{
    /**
     * 활성화된 공통 코드를 정렬하여 반환한다.
     */
    public function getActiveOrdered(array $columns = ['*']): Collection;
}
