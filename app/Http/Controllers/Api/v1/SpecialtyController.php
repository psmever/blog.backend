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

    public function weather()
    {

        return Response::success($this->SpecialtyServices->getNowWeather());
    }
}
