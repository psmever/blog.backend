<?php

namespace App\Services;

use App\Repositories\CommonCodeRepositoryInterface;

class SystemService
{
    public function __construct(
        private readonly CommonCodeRepositoryInterface $commonCodes
    ) {
    }

    public function getBaseData(): array
    {
        $codes = $this->commonCodes->getActiveOrdered([
            'group_key',
            'code',
            'label',
            'description',
            'sort_order',
            'meta',
        ]);

        $grouped = $codes
            ->groupBy('group_key')
            ->map(function ($items) {
                return $items->values()->map(fn ($code) => [
                    'code' => $code->code,
                    'label' => $code->label,
                    'description' => $code->description,
                    'sort_order' => $code->sort_order,
                    'meta' => $code->meta,
                ])->toArray();
            })
            ->toArray();

        return [
            'app' => [
                'name' => config('app.name'),
                'env' => app()->environment(),
                'url' => config('app.url'),
                'version' => app()->version(),
            ],
            'server_time' => now()->toIso8601String(),
            'common_codes' => $grouped,
        ];
    }
}
