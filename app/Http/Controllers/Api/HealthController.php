<?php

namespace App\Http\Controllers\Api;

class HealthController extends ApiBaseController
{
    public function index()
    {
        return $this->responseSuccess([
            'app' => config('app.name'),
            'version' => app()->version(),
            'env' => app()->environment(),
        ]);
    }
}
