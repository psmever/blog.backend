<?php

namespace Tests\Unit\Http\Controllers\Api\v1\SpecialtyController;

use Tests\TestCase;
// use PHPUnit\Framework\TestCase;

use Illuminate\Support\Facades\DB;
use App\Models\VilageFcstinfoMaster;
use App\Models\VilageFcstinfo;
use App\Models\Weathers;
use App\Models\CovidMaster;
use App\Models\CovidState;

class WeatherTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * 날씨 데이터 없을때.
     * 정상 데이터 일때.
     */

    public function test_날씨_조회_테스트_데이터_없을때()
    {
        VilageFcstinfoMaster::factory()->create();
        VilageFcstinfo::factory()->create();
        // Weathers::factory()->create();

        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/specialty/weather');
        // $response->dump();
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.exception.model_not_found_exception'),
            ]
        ]);

    }

    public function test_날씨_조회_테스트_테이터_있을때()
    {
        VilageFcstinfoMaster::factory()->create();
        VilageFcstinfo::factory()->create();
        Weathers::factory()->create();

        // $vilagefcstinfomaster = VilageFcstinfoMaster::all();
        // $vilagefcstinfo = VilageFcstinfo::all();
        // $weathers = Weathers::all();

        // print_r($vilagefcstinfomaster->toArray());
        // print_r($vilagefcstinfo->toArray());
        // print_r($weathers->toArray());

        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/specialty/weather');
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                "vilage" => [
                    "area_code",
                    "step1",
                    "step2",
                    "step3",
                    "vilage_name",
                    "vilage_short_name"
                ],
                "fcst" => [
                    "fcstdate",
                    "fcstdate_time",
                    "fcstdate_string",
                    "fcsttime",
                    "fcsttime_time",
                    "fcsttime_string",
                ],
                "T1H",
                "RN1",
                "SKY",
                "UUU",
                "VVV",
                "REH",
                "PTY",
                "LGT",
                "VEC",
                "WSD",
                "created",
                "updated",
            ]
        ]);
    }
}
