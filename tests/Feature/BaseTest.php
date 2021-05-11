<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

//    /**
//     * A basic feature test example.
//     *
//     * @return void
//     */
//    public function test_example()
//    {
//        $response = $this->get('/');
//
//        $response->assertStatus(200);
//    }

//    /**
//     * 전체 테이블 리스트 출력.
//     */
//    public function test_database_table_list()
//    {
//        self::printTotalTableList();
//    }

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
     * 기본 페이지 테스트
     *
     * @return void
     */
    public function test_base_server_landing_page()
    {
        $response = $this->get('/');
        // $response->dump();
        $response->assertStatus(200);
    }
}
