<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\SystemService;

class SystemController extends ApiBaseController
{
    public function __construct(
        private readonly SystemService $systemService
    ) {}

    public function index()
    {
        return $this->responseSuccess(
            $this->systemService->getBaseData()
        );
    }
}
