<?php

namespace App\Http\Controllers\Api;

class HealthController extends ApiBaseController
{
    public function index()
    {
        if (app()->isProduction()) {
            return $this->responseSuccess(['status' => 'ok']);
        }

        return $this->responseSuccess([
            'app' => config('app.name'),
            'version' => app()->version(),
            'env' => app()->environment(),
        ]);
    }
}
