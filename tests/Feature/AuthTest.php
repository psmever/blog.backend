<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AuthTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    // TODO 2020-08-27 23:49 로그인 유닛 테스트.
    // $tableData = DB::table('oauth_clients')->get();
    // print_r($tableData);
    // 이메일 없을떄.
    public function test_auth_login_check_email_required()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => '', 'password' => '']);
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.email_required')
        ]);
    }

    // 이메일 형식이 아닐때.
    public function test_auth_login_check_email_not_validate()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'test', 'password' => '']);
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.email_not_validate')
        ]);
    }

    // 없는 사용자 이메일.
    public function test_auth_login_check_email_exists()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'test1@gmail.com', 'password' => '']);
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.email_exists')
        ]);
    }

    // 패스워드 없을떄.
    public function test_auth_login_check_password_required()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'test_admin@gmail.com', 'password' => '']);
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.password_required')
        ]);
    }

    // 비밀번호 틀렸을떄.
    public function test_auth_login_check_password_fail()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'test_admin@gmail.com', 'password' => '12121']);
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.password_fail')
        ]);
    }

    // 로그인 성공
    public function test_auth_login_success_check()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'test_admin@gmail.com', 'password' => '1212']);
        // $response->dump();
        $response->assertOk();
        $response->assertJsonStructure([
            "token_type",
            "expires_in",
            "access_token",
            "refresh_token"
        ]);
    }
}
