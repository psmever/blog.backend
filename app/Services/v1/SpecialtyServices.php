<?php

namespace App\Services\v1;

use App\Repositories\v1\SpecialtyRepository;
use Illuminate\Support\Carbon;

class SpecialtyServices
{
    protected $specialtyRepository;

    function __construct(SpecialtyRepository $specialtyRepository) {
        $this->specialtyRepository = $specialtyRepository;
    }

    /*
     * 날씨 정보.
     */
    public function getNowWeather()
    {
        $fcstDate = Carbon::Now()->format('Ymd');
        $fcstTime = Carbon::Now()->format('H00');

        echo $fcstDate.PHP_EOL;
        echo $fcstTime.PHP_EOL;

        $task = $this->specialtyRepository->getTopWeatherData();


        return [
            "status" => false
        ];
    }
}
