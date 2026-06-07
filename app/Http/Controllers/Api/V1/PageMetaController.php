<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\PageMetaService;
use Illuminate\Http\Request;

class PageMetaController extends ApiBaseController
{
    public function __construct(
        private readonly PageMetaService $pageMeta
    ) {}

    public function show(Request $request)
    {
        $payload = $request->validate([
            'url' => ['required', 'string', 'max:2000'],
        ]);

        return $this->responseSuccess(
            $this->pageMeta->getMeta((string) $payload['url'])
        );
    }
}
