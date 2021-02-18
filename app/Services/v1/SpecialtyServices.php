<?php

namespace App\Services\v1;

use App\Repositories\v1\SpecialtyRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
        $jsonArea = json_decode(Storage::disk('sitedata')->get('weather_area_code.json'), true);

        $areaCodes = $jsonArea['area_code'];

        $params = [
            "fcstDate" => Carbon::Now()->format('Ymd'),
            "fcstTime" => Carbon::Now()->format('H00')
        ];


        return array_map(function($area_code) use ($params) {

            $task = $this->specialtyRepository->getTopWeatherData([
                'area_code' => $area_code,
                'fcstDate' => $params['fcstDate'],
                'fcstTime' => $params['fcstTime']
            ]);


            return [];

        }, $areaCodes);



    }
}
