<?php

namespace App\Services;

use App\Repositories\SpecialtyRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class SpecialtyServices
{
    /**
     * @var SpecialtyRepository
     */
    protected SpecialtyRepository $specialtyRepository;

    /**
     * @var array|string[][]
     */
    public array $windCode = [
        0 => [ 'kr' => '북', 'en' => 'N', ],
        1 => [ 'kr' => '북북동', 'en' => 'NNE', ],
        2 => [ 'kr' => '북동', 'en' => 'NE', ],
        3 => [ 'kr' => '동북동', 'en' => 'ENE', ],
        4 => [ 'kr' => '동', 'en' => 'E', ],
        5 => [ 'kr' => '동남동', 'en' => 'ESE', ],
        6 => [ 'kr' => '남동', 'en' => 'SE', ],
        7 => [ 'kr' => '남남동', 'en' => 'SSE', ],
        8 => [ 'kr' => '남', 'en' => 'S', ],
        9 => [ 'kr' => '남남서', 'en' => 'SSW', ],
        10 => [ 'kr' => '남서', 'en' => 'SW', ],
        11 => [ 'kr' => '서남서', 'en' => 'WSW', ],
        12 => [ 'kr' => '서', 'en' => 'W', ],
        13 => [ 'kr' => '서북서', 'en' => 'WWW', ],
        14 => [ 'kr' => '북서', 'en' => 'NW', ],
        15 => [ 'kr' => '북북서', 'en' => 'NNW', ],
    ];

    /**
     * SpecialtyServices constructor.
     * @param SpecialtyRepository $specialtyRepository
     */
    function __construct(SpecialtyRepository $specialtyRepository) {
        $this->specialtyRepository = $specialtyRepository;
    }

    /**
     * 날씨 정보.
     * @return array
     * @throws FileNotFoundException
     */
//    public function getNowWeatherData(): array
//    {
//        $jsonArea = json_decode(Storage::disk('sitedata')->get('weather_area_code.json'), true);
//
//        $areaCodes = [];
//
//        // unit testing 코드.
//        if(env('APP_ENV') === 'testing') {
//            $areaCodes[] = $jsonArea['area_code'][0];
//        } else {
//            $areaCodes = $jsonArea['area_code'];
//        }
//
//        $params = [
//            "fcstDate" => Carbon::Now()->format('Ymd'),
//            "fcstTime" => Carbon::Now()->format('H00')
//        ];
//
//        return array_map(function($area_code) use ($params) {
//
//            $task = $this->specialtyRepository->getTopWeatherData([
//                'area_code' => $area_code,
//                'fcstDate' => $params['fcstDate'],
//                'fcstTime' => $params['fcstTime']
//            ])->toArray();
//
//            if(empty($task['weathers'])) {
//                $task = $this->specialtyRepository->getTopWeatherDataSub([
//                    'area_code' => $area_code,
//                    'fcstDate' => $params['fcstDate'],
//                ])->toArray();
//            }
//
//            $weathers = $task['weathers'][0] ?? null;
//            $vilage = $weathers['vilage'] ?? null;
//
//            if($weathers == null || $vilage == null) {
//                throw new ModelNotFoundException();
//            }
//
//            // fcstDate '예측일자.'
//            // fcstTime '예측시간.'
//            // T1H  '기온.'
//            // RN1  '1시간 강수량.'
//            // SKY  '하늘상태(맑음(1), 구름많음(3), 흐림(4))'
//            // UUU  '동서바람성분.'
//            // VVV  '남북바람성분.'
//            // REH  '습도.'
//            // PTY  '강수형태(없음(0), 비(1), 비/눈(2), 눈(3), 소나기(4), 빗방울(5), 빗방울/눈날림(6), 눈날림(7))'
//            // LGT  '낙뢰.'
//            // VEC  '풍향.'
//            // WSD  '풍속.'
//            // created 데이터 생성 시간.
//            // updated 데이터 업데이트 시간.
//
//            $step1 = $vilage['step1'];
//            $step2 = $vilage['step2'];
//            $step3 = $vilage['step3'];
//
//            if($step3) {
//                $shortName = $step3;
//            } else if($step2) {
//                $shortName = $step2;
//            } else if($step1) {
//                $shortName = $step1;
//            }
//
//            return [
//                'vilage' => [
//                    "area_code" => $vilage['area_code'],
//                    "step1" => $vilage['step1'],
//                    "step2" => $vilage['step2'],
//                    "step3" => $vilage['step3'],
//                    "vilage_name" => trim("{$vilage['step1']} {$vilage['step2']} {$vilage['step3']}"),
//                    "vilage_short_name" => trim($shortName),
//                ],
//                'fcst' => [
//                    'fcstdate' => $weathers['fcstDate'],
//                    'fcstdate_time' => Carbon::parse($weathers['fcstDate'])->format('Y-m-d'),
//                    'fcstdate_string' => Carbon::parse($weathers['fcstDate'])->format('Y년 m월 d일'),
//                    'fcsttime' => $weathers['fcstTime'],
//                    'fcsttime_time' => Carbon::parse($weathers['fcstTime'])->format('H:i'),
//                    'fcsttime_string' => Carbon::parse($weathers['fcstTime'])->format('H시 i분'),
//                ],
//                'T1H' => $weathers['T1H'],
//                'RN1' => $weathers['RN1'],
//                'SKY' => $weathers['SKY'],
//                'UUU' => $weathers['UUU'],
//                'VVV' => $weathers['VVV'],
//                'REH' => $weathers['REH'],
//                'PTY' => $weathers['PTY'],
//                'LGT' => $weathers['LGT'],
//                'VEC' => $weathers['VEC'],
//                'WSD' => $weathers['WSD'],
//                'created' => Carbon::parse($weathers['created_at'])->format('Y-m-d H:i:s'),
//                'updated' => Carbon::parse($weathers['updated_at'])->format('Y-m-d H:i:s'),
//            ];
//
//        }, $areaCodes);
//    }

    /**
     * 날씨 정보.
     * @return array
     * @throws FileNotFoundException
     */
    public function getNowWeather(): array
    {
        $jsonArea = json_decode(Storage::disk('sitedata')->get('weather_area_code.json'), true);

        $areaCodes = [];

        // unit testing 코드.
        if(env('APP_ENV') === 'testing') {
            $areaCodes[] = $jsonArea['area_code'][0];
        } else {
            $areaCodes = $jsonArea['area_code'];
        }

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

            if(empty($task['weathers'])) {
                $task = $this->specialtyRepository->getTopWeatherDataSub([
                    'area_code' => $area_code,
                    'fcstDate' => $params['fcstDate'],
                ])->toArray();
            }

            $weathers = $task['weathers'][0] ?? null;
            $vilage = $weathers['vilage'] ?? null;

            if($weathers == null || $vilage == null) {
                throw new ModelNotFoundException();
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

            // 맑음(1), 구름많음(3), 흐림(4)

            switch ($weathers['SKY']) {
                case '1':
                    $skyString = "맑음";
                    break;
                case '3':
                    $skyString = "구름많음";
                    break;
                case '4':
                    $skyString = "흐림";
                    break;
                default:
                    $skyString = "없음";
                    break;
            }


            $fcstTime = (int) $weathers['fcstTime'];
            $pty = (int) $weathers['PTY'];
            $sky = (int) $weathers['SKY'];
            $t1h = (int) $weathers['T1H'];
            $vec = (int) $weathers['VEC'];
            $wsd = (int) $weathers['WSD'];
            $reh = (int) $weathers['REH'];


            // 밤인지 낮인지.
            if(1900 > $fcstTime && $fcstTime > 0600) {
                $DayAndNight = "day";
            } else {
                $DayAndNight = "night";
            }

            // 하늘 아이콘 설정.
            $skyIconCode = "";
            if($pty > 0) {
                switch ($pty) {
                    case 1:
                        $skyIconCode = "S06080";
                        break;
                    case 2:
                        $skyIconCode = "S06120";
                        break;
                    case 3:
                        $skyIconCode = "S06110";
                        break;
                    case 4:
                        $skyIconCode = "S06070";
                        break;
                    case 5:
                        $skyIconCode = "S06200";
                        break;
                    case 6:
                        $skyIconCode = "S06220";
                        break;
                    case 7:
                        $skyIconCode = "S06210";
                        break;
                    default:
                        $skyString = "";
                        break;
                }

            } else {
                switch ($sky) {
                    case 1:
                        $skyIconCode = $DayAndNight == 'day' ? "S06010" : "S06011";
                        break;
                    case 3:
                        $skyIconCode = $DayAndNight == 'day' ? "S06030" : "S06031";
                        break;
                    case 4:
                        $skyIconCode = "S06040";
                        break;
                    default:
                        $skyIconCode = "";
                        break;
                }
            }

            // 풍향 계산.
            // FIXME: $vec 값이 음수로 들어올 경우가 있는데?? (db create_at: 2021-03-25 13:00:02)
            $wind = abs(floor(($vec + 22.5 * 0.5) / 22.5));

            return [
                "time" => Carbon::parse($weathers['fcstTime'])->format('H:i'),
                "vilage_name" => trim("{string $shortName}"),
                "sky_icon" => env('MEDIA_URL').'/storage/icon/weathers/'.$skyIconCode.'.png',
                "temperature" => "{string $t1h}°C",
                "sky" => $skyString,
                "wind" => "{$this->windCode[$wind]['kr']} {$wsd}m/s",
                "humidity" => $reh.'%',
            ];
        }, $areaCodes);
    }

    /**
     * 코로나 현황
     * @return array
     */
//    public function getCovidStateData(): array
//    {
//        return array_map(function($item) {
//            $covidState = $item["covid_state"] ?? null;
//
//            if($covidState == null) {
//                throw new ModelNotFoundException();
//            }
//
//            return [
//                "title" => $item["title"],
//                "gubun" => $item["gubun"],
//                "gubun_en" => $item["gubun_en"],
//                "state" => [
//                    "seq" => $covidState["seq"],
//                    "createdt" => $covidState["createdt"],
//                    "deathcnt" => $covidState["deathcnt"],
//                    "incdec" => $covidState["incdec"],
//                    "isolclearcnt" => $covidState["isolclearcnt"],
//                    "qurrate" => $covidState["qurrate"],
//                    "stdday" => $covidState["stdday"],
//                    "updatedt" => $covidState["updatedt"],
//                    "defcnt" => $covidState["defcnt"],
//                    "isolingcnt" => $covidState["isolingcnt"],
//                    "overflowcnt" => $covidState["overflowcnt"],
//                    "localocccnt" => $covidState["localocccnt"],
//                    "created_at" => Carbon::parse($covidState["created_at"])->format('Y-m-d H:i:s'),
//                    "updated_at" => Carbon::parse($covidState["updated_at"])->format('Y-m-d H:i:s'),
//                ]
//            ];
//        }, $this->specialtyRepository->getCovidState()->toArray());
//    }

    /**
     * 코로나 현황.
     * @return array
     */
    public function getCovidState(): array
    {
        $task = $this->specialtyRepository->getCovidTodayTotal()->toArray();

        $covid_totals = $task['covid_total'] ?? null;

        if($covid_totals === null || count($task['covid_total']) === 0) {
            throw new ModelNotFoundException();
        }

        return array_map(function($item) {
            return [
                'defcnt' => $item['defcnt'], // 확진자수.
                'isolclearcnt' => $item['isolclearcnt'], // 격리해제.
                'deathcnt' => $item['deathcnt'], // 사망수.
                'incdec' => $item['incdec'], // 전일대비 증감.
            ];
        } , $covid_totals);
    }
}
