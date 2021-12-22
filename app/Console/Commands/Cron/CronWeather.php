<?php

namespace App\Console\Commands\Cron;

use App\Exceptions\CustomException;
use App\Models\Weathers;
use Illuminate\Console\Command;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use App\Models\VilageFcstinfoMaster;
use App\Jobs\ServerSlackNotice;
use Illuminate\Support\Facades\Storage;

class CronWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:weather {option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '기상청 날씨 데이터 업데이트.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $option = $this->argument('option');

        if($option === 'get') {
            $this->getWeather();
        }

        return 0;
    }

    /**
     * @throws FileNotFoundException
     * @throws CustomException
     */
    public function getWeather(): int
    {

        if(!Storage::disk('sitedata')->exists("weather_area_code.json")) {
            echo "json file not found";
            return 0;
        }

        $readData = json_decode(Storage::disk('sitedata')->get('weather_area_code.json'), true);

        // 행정구역 코드.
        $AreaCode = $readData['area_code'];

        $serviceKey = env('APIS_DATA_GO_KR_SERVICE_KEY');
        $pageNo = 1;
        $numOfRows = 100;
        $dataType = "JSON";
        $base_date = Carbon::now()->subHours()->format('Ymd');
        $base_time = Carbon::now()->subHours()->format('H00');


        try {

            foreach ($AreaCode as $element) {

                $taskAreaCode = $element;

                $result = VilageFcstinfoMaster::with(['vilage_fcstinfo' => function($query) use ($taskAreaCode) {
                    $query->where('area_code', $taskAreaCode);
                }])->where('active', 'Y')->first()->toArray();

                $vilage_fcstinfo = $result['vilage_fcstinfo'];

                $area_id = trim($vilage_fcstinfo['id']);

                $gridX = trim($vilage_fcstinfo['grid_x']);
                $gridY = trim($vilage_fcstinfo['grid_y']);

                $nx = $gridX;
                $ny = $gridY;

                $response = Http::withOptions([
                    'debug' => false,
                    'useUrlEncoding' => false,
                ])->get("http://apis.data.go.kr/1360000/VilageFcstInfoService_2.0/getUltraSrtNcst?serviceKey={$serviceKey}&pageNo={$pageNo}&numOfRows={$numOfRows}&dataType={$dataType}&base_date={$base_date}&base_time={$base_time}&nx={$nx}&ny={$ny}");

                if($response->successful())
                {

                    $newData = [];

                    $collection = collect($response->json());

                    // 12 시 넘어 갈때 시간 체크 에러..
                    if(!array_key_exists('body' , $collection->toArray()['response'])) {
                        return 0;
                    }


                    $item = $collection->toArray()['response']['body']['items']['item'];
//                    $pageNo = $collection->toArray()['response']['body']['pageNo'];
//                    $numOfRows = $collection->toArray()['response']['body']['numOfRows'];

                    foreach ($item as $item_element)
                    {
                        $category = $item_element['category'];
                        $fcstDate = $item_element['baseDate'];
                        $fcstTime = $item_element['baseTime'];
                        $fcstValue = $item_element['obsrValue'];

                        if(empty($newData[$fcstDate][$fcstTime][$category])) {
                            $newData[$fcstDate][$fcstTime][$category] = $fcstValue;
                        }
                    }

                    foreach ($newData as $fcstDate => $step1)
                    {
                        foreach ($step1 as $fcstTime => $step2)
                        {
                            Weathers::updateOrCreate(
                                ['area_code_id'=> $area_id, 'fcstDate' => $fcstDate, 'fcstTime' => $fcstTime],
                                [
                                    'T1H' => $step2["T1H"],
                                    'RN1' => $step2["RN1"],
                                    'SKY' => $step2["SKY"] ?? '',
                                    'UUU' => $step2["UUU"],
                                    'VVV' => $step2["VVV"],
                                    'REH' => $step2["REH"],
                                    'PTY' => $step2["PTY"],
                                    'LGT' => $step2["LGT"] ?? '',
                                    'VEC' => $step2["VEC"],
                                    'WSD' => $step2["WSD"]
                                ],
                            );
                        }
                    }

                    // $job = new ServerSlackNotice((object) [
                    //     'type' => 'notice',
                    //     'message' => '날씨 정보를 가지고 오는데 성공 했습니다.'
                    // ]);
                    // dispatch($job);

                } else {

                    $job = new ServerSlackNotice((object) [
                        'type' => 'exception',
                        'message' => '날씨 정보를 가지고 오는데 실패 했습니다.(002)'
                    ]);
                    dispatch($job);
                }
            }

        } catch (\Exception $e) {
            $job = new ServerSlackNotice((object) [
                'type' => 'exception',
                'message' => '날씨 정보를 가지고 오는데 실패 했습니다.(001)'
            ]);
            dispatch($job);

            $exceptionMessage = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            throw new CustomException($exceptionMessage);
        }

        return 0;
    }
}
