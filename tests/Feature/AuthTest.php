<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AuthTest extends TestCase
{

    public $user_email;
    public $user_password;
    public $access_token;
    public $refresh_token;

    public function setUp(): void
    {
        parent::setUp();

        $user = $this->userCreate();

        $this->user_email = $user->email;
        $this->user_password = $user->password;
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
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'testtest@gmail.com', 'password' => '']);
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
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => $this->user_email, 'password' => '']);
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
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => $this->user_email, 'password' => '1111']);
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
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => $this->user_email, 'password' => 'password']);
        // $response->dump();
        $response->assertOk();
        $response->assertJsonStructure([
            "token_type",
            "expires_in",
            "access_token",
            "refresh_token"
        ]);
    }

    // 로그아웃 로그인 되어 있지 않을때.
    public function test_auth_login_error_check()
    {
        $header = $this->getTestApiHeaders();
        $header['Authorization'] = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9';

        $response = $this->withHeaders($header)->json('POST', '/api/v1/auth/login-check', ['email' => $this->user_email, 'password' => $this->user_password]);
        // $response->dump();
        $response->assertUnauthorized();
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.unauthorized')
        ]);
    }

    // 로그아웃 성공.
    public function test_auth_login_check()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => $this->user_email,
            "password" => 'password'
        ]);
        $access_token = $response['access_token'];

        $header = $this->getTestApiHeaders();
        $header['Authorization'] = 'Bearer '.$access_token;

        $response = $this->withHeaders($header)->json('POST', '/api/v1/auth/login-check', ['email' => $this->user_email, 'password' => $this->user_password]);
        // $response->dump();
        $response->assertOk();
        $response->assertJsonStructure(
            $this->getDefaultSuccessJsonType()
        );
        $response->assertJsonStructure([
            'message',
            'result' => [
                "user_uuid",
                "user_type" => [
                    "code",
                    "code_name"
                ],
                "user_level" => [
                    "code",
                    "code_name"
                ]
            ]
        ]);
    }
}
