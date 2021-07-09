<?php

namespace Tests\Unit;

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

    /**
     * 마이그레이션 시드 체크
     *
     * @return void
     */
    public function test_base_server_migrate()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'root@gmail.com',
        ]);
    }

    /**
     * 클라이언트 코드 없을때
     *
     * @return void
     */
    public function test_base_server_exception_not_found_client_type_check()
    {
        $testHeader = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = $this->withHeaders($testHeader)->json('GET', '/api/system/check-status');
        // $response->dump();
        $response->assertStatus(412);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.exception.clienttype')
            ]
        ]);
    }
}