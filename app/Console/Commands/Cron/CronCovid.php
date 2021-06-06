<?php

namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use App\Models\CovidMaster;
use App\Models\CovidState;

use App\Jobs\ServerSlackNotice;

class CronCovid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:covid {option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $option = $this->argument('option');

        if($option === 'get') {
            $this->getCovid();
        }

        return 0;
    }

    public function getCovid()
    {
        $serviceKey = env('APIS_DATA_GO_KR_SERVICE_KEY');
        $pageNo = 1;
        $numOfRows = 19;
        $startCreateDt = Carbon::now()->subHours()->format('Ymd');
        $endCreateDt = Carbon::now()->subHours()->format('Ymd');

        //    Schema::disableForeignKeyConstraints();
        //    CovidMaster::truncate();
        //    CovidState::truncate();
        //    Schema::enableForeignKeyConstraints();

        try {

            $response = Http::withOptions([
                'debug' => false,
                'useUrlEncoding' => false,
            ])->get("http://openapi.data.go.kr/openapi/service/rest/Covid19/getCovid19SidoInfStateJson?serviceKey={$serviceKey}&pageNo={$pageNo}&numOfRows={$numOfRows}&startCreateDt={$startCreateDt}&endCreateDt={$endCreateDt}");

            if($response->successful())
            {
                $xml = simplexml_load_string($response->body(),'SimpleXMLElement', LIBXML_NOCDATA);
                $json = json_encode($xml);
                $resultArray = json_decode($json, true);

                $items = $resultArray['body']['items']['item'];

                array_map(function($item){
                    [
                        'seq' => $seq,                              // 게시글번호(국내 시도별 발생현황 고유값)
                        'createDt' => $createDt,                    // 등록일시분초
                        'deathCnt' => $deathCnt,                    // 사망자 수
                        'gubun' => $gubun,                          // 시도명(한글)
//                        'gubunCn' => $gubunCn,                      // 시도명(중국어)
                        'gubunEn' => $gubunEn,                      // 시도명(영어)
                        'incDec' => $incDec,                        // 전일대비 증감 수
                        'isolClearCnt' => $isolClearCnt,            // 격리 해제 수
                        'qurRate' => $qurRate,                      // 10만명당 발생률
                        'stdDay' => $stdDay,                        // 기준일시
                        'updateDt' => $updateDt,                    // 수정일시분초
                        'defCnt' => $defCnt,                        // 확진자 수
                        'isolIngCnt' => $isolIngCnt,                // 격리중 환자수
                        'overFlowCnt' => $overFlowCnt,              // 해외유입 수
                        'localOccCnt' => $localOccCnt,              // 지역발생 수
                    ] = get_object_vars((object) $item);

                    $gubunEnString = strtolower(preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $gubunEn));


                    $masterInfo = CovidMaster::firstOrCreate(
                        ['title' => $gubunEnString],
                        ['gubun' => $gubun, 'gubun_en' => $gubunEn]
                    );

                    CovidState::updateOrCreate(
                        ['gubun_id'=> $masterInfo->id, 'seq' => $seq],
                        [
                            'seq' => $seq,
                            'createdt' => $createDt,
                            'deathcnt' => $deathCnt,
                            'incdec' => $incDec,
                            'isolclearcnt' => $isolClearCnt,
                            'qurrate' => $qurRate,
                            'stdday' => $stdDay,
                            'updatedt' => $updateDt,
                            'defcnt' => $defCnt,
                            'isolingcnt' => $isolIngCnt,
                            'overflowcnt' => $overFlowCnt,
                            'localocccnt' => $localOccCnt,
                        ],
                    );
                }, $items);

                // $job = new ServerSlackNotice((object) [
                //     'type' => 'notice',
                //     'message' => '코로나 정보를 가지고 오는데 성공 했습니다.'
                // ]);
                // dispatch($job);

            } else {
                $job = new ServerSlackNotice((object) [
                    'type' => 'exception',
                    'message' => '코로나 정보를 가지고 오지 못했습니다(002).'
                ]);
                dispatch($job);
            }

        } catch (\Exception $e) {
            $job = new ServerSlackNotice((object) [
                'type' => 'exception',
                'message' => '코로나 정보를 가지고 오지 못했습니다(001).'
            ]);
            dispatch($job);

            $exceptionMessage = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            throw new CustomException($exceptionMessage);
        }
    }
}
