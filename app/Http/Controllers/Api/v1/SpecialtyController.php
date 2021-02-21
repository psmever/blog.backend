<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\v1\SpecialtyServices;

class SpecialtyController extends ApiRootController
{
    protected $SpecialtyServices;

    public function __construct(SpecialtyServices $specialtyServices)
    {
        $this->SpecialtyServices = $specialtyServices;
    }

    // 동네 날씨 예보.
    public function weather()
    {
        return Response::success_only_data($this->SpecialtyServices->getNowWeather());
    }
}
