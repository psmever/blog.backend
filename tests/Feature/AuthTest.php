<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AuthTest extends TestCase
{

    public $user_email;
    public $access_token;
    public $refresh_token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user_email = \App\User::where('user_level', 'S02900')->orderBy('id', 'ASC')->first()->email;
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
            "error" => [
                'error_message' => __('default.login.email_required')
            ]
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
            "error" => [
                'error_message' => __('default.login.email_not_validate')
            ]
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

        $response = $this->withHeaders($header)->json('GET', '/api/v1/auth/login-check');
        // $response->dump();
        $response->assertUnauthorized();
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.unauthorized')
        ]);
    }

    // 로그인 상태 체크.
    public function test_auth_login_check()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => $this->user_email,
            "password" => 'password'
        ]);
        $access_token = $response['access_token'];

        $header = $this->getTestApiHeaders();
        $header['Authorization'] = 'Bearer '.$access_token;

        $response = $this->withHeaders($header)->json('GET', '/api/v1/auth/login-check');
        // $response->dump();
        $response->assertOk();
        $response->assertJsonStructure([
            "user_uuid",
            "user_type" => [
                "code",
                "code_name"
            ],
            "user_level" => [
                "code",
                "code_name"
            ]
        ]);
    }

    // 토큰 리프레쉬-
    // FIXME 토큰 없이 리프레쉬 시도시 에러코드, 메시지 수정??
    public function test_TokenRefresh_로그인정보없을때()
    {
        $header = $this->getTestApiHeaders();

        $response = $this->withHeaders($header)->json('POST', '/api/v1/auth/token-refresh');
        // $response->dump();
        $response->assertStatus(412);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.refresh_token_not_fount')
        ]);
    }

    public function test_TokenRefresh_리프레쉬토큰_에러()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => $this->user_email,
            "password" => 'password'
        ]);
        $access_token = $response['access_token'];

        $header = $this->getTestApiHeaders();
        $header['Authorization'] = 'Bearer '.$access_token;

        $response = $this->withHeaders($header)->json('POST', '/api/v1/auth/token-refresh', [
            "refresh_token" => "asdasdasdasdasd"
        ]);
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure(
            $this->getDefaultErrorJsonType()
        )->assertJsonFragment([
            "error_message" => __('default.login.refresh_token_fail')
        ]);

    }

    public function test_TokenRefresh_성공_했을때()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => $this->user_email,
            "password" => 'password'
        ]);
        $access_token = $response['access_token'];
        $refresh_token = $response['refresh_token'];

        $header = $this->getTestApiHeaders();
        $header['Authorization'] = 'Bearer '.$access_token;

        $response = $this->withHeaders($header)->json('POST', '/api/v1/auth/token-refresh', [
            "refresh_token" => $refresh_token
        ]);
        // $response->dump();
        $response->assertOk();
        $response->assertJsonStructure([
            "token_type",
            "expires_in",
            "access_token",
            "refresh_token"
        ]);
    }

    // 로그아웃 - 성공
    public function test_auth_logout()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => $this->user_email,
            "password" => 'password'
        ]);
        $access_token = $response['access_token'];

        $header = $this->getTestApiHeaders();
        $header['Authorization'] = 'Bearer '.$access_token;

        $response = $this->withHeaders($header)->json('POST', '/api/v1/auth/logout');
        // $response->dump();
        $response->assertStatus(204);
    }
}
