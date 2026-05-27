<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Collection;

interface TagRepositoryInterface
{
    /**
     * @param  array<int, string>  $names
     * @return Collection<int, Tag>
     */
    public function findOrCreateByNames(array $names): Collection;
}
