<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use App\Services\SpecialtyServices;
use Illuminate\Support\Facades\Response;

/**
 * Class SpecialtyController
 * @package App\Http\Controllers\Api\v1
 */
class SpecialtyController extends ApiRootController
{
    /**
     * @var SpecialtyServices
     */
    protected SpecialtyServices $SpecialtyServices;

    /**
     * SpecialtyController constructor.
     * @param SpecialtyServices $specialtyServices
     */
    public function __construct(SpecialtyServices $specialtyServices)
    {
        $this->SpecialtyServices = $specialtyServices;
    }

    /**
     * 동네 날씨 예보.
     * @return mixed
     */
    public function weather()
    {
        return Response::success_only_data($this->SpecialtyServices->getNowWeather());
    }

    /**
     * 코로나 현황.
     * @return mixed
     */
    public function covid()
    {
        return Response::success_only_data($this->SpecialtyServices->getCovidState());
    }
}
