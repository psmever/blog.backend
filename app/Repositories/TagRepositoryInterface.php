<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface TagRepositoryInterface
{
    /**
     * @param  array<int, string>  $names
     * @return Collection<int, \App\Models\Tag>
     */
    public function findOrCreateByNames(array $names): Collection;
}
