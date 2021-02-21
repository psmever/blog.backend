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
            ])->toArray();

            $weathers = $task['weathers'][0] ?? null;
            $vilage = $weathers['vilage'] ?? null;

            if($weathers == null || $vilage == null) {
                return null;
            }

            // fcstDate '예측일자.'
            // fcstTime '예측시간.'
            // T1H  '기온.'
            // RN1  '1시간 강수량.'
            // SKY  '하늘상태(맑음(1), 구름많음(3), 흐림(4))'
            // UUU  '동서바람성분.'
            // VVV  '남북바람성분.'
            // REH  '습도.'
            // PTY  '강수형태(없음(0), 비(1), 비/눈(2), 눈(3), 소나기(4), 빗방울(5), 빗방울/눈날림(6), 눈날림(7))'
            // LGT  '낙뢰.'
            // VEC  '풍향.'
            // WSD  '풍속.'
            // created 데이터 생성 시간.
            // updated 데이터 업데이트 시간.

            $step1 = $vilage['step1'];
            $step2 = $vilage['step2'];
            $step3 = $vilage['step3'];

            if($step3) {
                $shortName = $step3;
            } else if($step2) {
                $shortName = $step2;
            } else if($step1) {
                $shortName = $step1;
            }

            return [
                'vilage' => [
                    "area_code" => $vilage['area_code'],
                    "step1" => $vilage['step1'],
                    "step2" => $vilage['step2'],
                    "step3" => $vilage['step3'],
                    "vilage_name" => trim("{$vilage['step1']} {$vilage['step2']} {$vilage['step3']}"),
                    "vilage_short_name" => trim("{$shortName}"),
                ],
                'fcst' => [
                    'fcstdate' => $weathers['fcstDate'],
                    'fcstdate_time' => Carbon::parse($weathers['fcstDate'])->format('Y-m-d'),
                    'fcstdate_string' => Carbon::parse($weathers['fcstDate'])->format('Y년 m월 d일'),
                    'fcsttime' => $weathers['fcstTime'],
                    'fcsttime_time' => Carbon::parse($weathers['fcstTime'])->format('H:i'),
                    'fcsttime_string' => Carbon::parse($weathers['fcstTime'])->format('H시 i분'),
                ],
                'T1H' => $weathers['T1H'],
                'RN1' => $weathers['RN1'],
                'SKY' => $weathers['SKY'],
                'UUU' => $weathers['UUU'],
                'VVV' => $weathers['VVV'],
                'REH' => $weathers['REH'],
                'PTY' => $weathers['PTY'],
                'LGT' => $weathers['LGT'],
                'VEC' => $weathers['VEC'],
                'WSD' => $weathers['WSD'],
                'created' => Carbon::parse($weathers['created_at'])->format('Y-m-d H:i:s'),
                'updated' => Carbon::parse($weathers['updated_at'])->format('Y-m-d H:i:s'),
            ];

        }, $areaCodes);
    }
}
