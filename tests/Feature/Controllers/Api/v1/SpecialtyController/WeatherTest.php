<?php

namespace Tests\Feature\Controllers\Api\v1\SpecialtyController;

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

    public function test_날씨_조회_테스트_데이터_없을때()
    {
        VilageFcstinfoMaster::factory()->create();
        VilageFcstinfo::factory()->create();
        // Weathers::factory()->create();

        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/specialty/weather');
//         $response->dump();
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

        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/specialty/weather');
//         $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                "time",
                "vilage_name",
                "sky_icon",
                "temperature",
                "sky",
                "wind",
                "humidity",
            ]
        ]);
    }
}
