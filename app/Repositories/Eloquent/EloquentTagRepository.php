<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\TagRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EloquentTagRepository implements TagRepositoryInterface
{
    public function findOrCreateByNames(array $names): Collection
    {
        if (count($names) === 0) {
            return collect();
        }

        return collect($names)
            ->map(function ($name) {
                [$key, $label] = $this->normalize($name);

                return Tag::query()->firstOrCreate(
                    ['key' => $key],
                    ['label' => $label]
                );
            })
            ->values();
    }

    private function normalize(string $name): array
    {
        $label = Str::of($name)->squish()->toString();
        $lower = Str::lower($label);
        $upper = Str::upper($label);

        if ($label === $lower || $label === $upper) {
            $label = Str::ucfirst($lower);
        }

        $slugSource = preg_replace('/[^\pL\pN]+/u', ' ', $lower);
        $slugSource = trim(preg_replace('/\s+/u', ' ', $slugSource ?? $lower));

        $key = Str::slug($slugSource);
        if ($key === '') {
            $key = Str::of($slugSource)->replace(' ', '-')->toString();
        }

        $key = Str::limit($key, 40, '');
        $label = Str::limit($label, 60, '');

        return [$key, $label];
    }
}
