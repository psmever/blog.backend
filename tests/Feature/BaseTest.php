<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use \App\User;
use Illuminate\Support\Facades\DB;

class BaseTest extends TestCase
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
            'email' => 'test@gmail.com',
        ]);
    }

    /**
     * 서버 상태 api 체크
     *
     * @return void
     */
    public function test_server_client_type_error_check() {

        $testHeader = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = $this->withHeaders($testHeader)->json('GET', '/api/system/check/status');
        $response->assertForbidden();
        $response->assertJsonStructure([
            'error_message'
        ])->assertJsonFragment([
            "error_message" => __('default.exception.clienttype')
        ]);
    }
}
