<?php

namespace Tests\Unit\Http\Controllers\Api\v1\SpecialtyController;

use Tests\TestCase;
// use PHPUnit\Framework\TestCase;

use Illuminate\Support\Facades\DB;
use App\Models\CovidMaster;
use App\Models\CovidState;

class CovidTest extends TestCase
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

    public function test_코로나_조회_테스트_데이터_없을때()
    {
        CovidMaster::factory()->create();

        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/specialty/covid');
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

    public function test_코로나_조회_테스트_데이터_있을때()
    {
        CovidMaster::factory()->create();
        CovidState::factory()->create();

        // print_r(CovidMaster::all()->toArray());
        // print_r(CovidState::all()->toArray);

        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/specialty/covid');
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                "title",
                "gubun",
                "gubun_en",
                "state" => [
                  "seq",
                  "createdt",
                  "deathcnt",
                  "incdec",
                  "isolclearcnt",
                  "qurrate",
                  "stdday",
                  "updatedt",
                  "defcnt",
                  "isolingcnt",
                  "overflowcnt",
                  "localocccnt",
                  "created_at",
                  "updated_at",
                ]
            ]
        ]);
    }
}
