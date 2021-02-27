<?php

namespace Tests\Unit\Http\Controllers\Api\v1\SpecialtyController;

use Tests\TestCase;
// use PHPUnit\Framework\TestCase;

use Illuminate\Support\Facades\DB;
use App\Models\VilageFcstinfoMaster;

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
        $users = VilageFcstinfoMaster::factory()->count(5)->suspended()->make();
    }
}
