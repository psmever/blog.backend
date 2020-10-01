<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;


class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    // 테스트용.
    // public function test전체테이블리스트()
    // {
    //     echo PHP_EOL.PHP_EOL;
    //     $tables = DB::select("SELECT name FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' UNION ALL SELECT name FROM sqlite_temp_master WHERE type IN ('table', 'view') ORDER BY 1");

    //     foreach($tables as $table)
    //     {
    //         echo $table->name.PHP_EOL;
    //     }
    //     echo PHP_EOL;
    // }

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
    public function test_server_landing_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 마이그레이션 시드 체크
     *
     * @return void
     */
    public function test_server_migrate()
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
    public function test_server_exception_not_found_client_type_check()
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

    /**
     * 없는 클라이언트 코드 일때.
     *
     * @return void
     */
    public function test_server_exception_not_client_type_code_check()
    {
        $testHeader = [
            'Request-Client-Type' => 'S010101',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = $this->withHeaders($testHeader)->json('GET', '/api/system/check-status');
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

    /**
     * 정상 클라이언트 헤더 일때.
     *
     * @return void
     */
    public function test_server_exception_client_type_check()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check-status');
        // $response->dump();
        $response->assertStatus(204);
    }

    /**
     * 없는 api 요청 일때.
     *
     * @return void
     */
    public function test_server_exception_not_found_check()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check-status1');
        // $response->dump();
        $response->assertNotFound();
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.exception.notfound')
            ]
        ]);
    }

    /**
     * 잘못된 method 요청 일때.
     *
     * @return void
     */
    public function test_server_exception_not_allowd_check()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/system/check-status');
        // $response->dump();
        $response->assertStatus(405);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.exception.notallowedmethod')
            ]
        ]);
    }

    /**
     * api 상태 체크 (Down).
     *
     * @return void
     */
    public function test_server_check_status_down()
    {
        $this->artisan('down');
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check-status');
        // $response->dump();
        $response->assertStatus(503);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.server.down')
            ]
        ]);
        $this->artisan('up');
    }

    /**
     * api 상태 체크 (Up).
     *
     * @return void
     */
    public function test_server_check_status_up()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check-status');
        $response->assertStatus(204);
    }
}
