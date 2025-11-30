<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\CommonCode;
use Illuminate\Http\Request;

class SystemController extends ApiBaseController
{
    public function index(Request $request)
    {
        $codes = CommonCode::query()
            ->active()
            ->orderBy('group_key')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get([
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

        return $this->responseSuccess([
            'app' => [
                'name' => config('app.name'),
                'env' => app()->environment(),
                'url' => config('app.url'),
                'version' => app()->version(),
            ],
            'server_time' => now()->toIso8601String(),
            'common_codes' => $grouped,
        ]);
    }
}
