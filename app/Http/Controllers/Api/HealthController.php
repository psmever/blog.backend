<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiBaseController;

class HealthController extends ApiBaseController
{
    public function index()
    {
        $info = [
            'status' => 'ok',
            'timestamp' => now()->toDateTimeString(),
            'env' => config('app.env'),
            'version' => app()->version(),
        ];

        return $this->success($info);
    }
}
