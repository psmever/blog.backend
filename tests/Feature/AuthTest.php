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
    public function test_auth_login()
    {
        // $tableData = DB::table('oauth_clients')->get();
        // print_r($tableData);

        // print_r($this->getTestApiHeaders());
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/auth/login', ['email' => 'test_admin@gmail.com', 'password' => '1212']);
        $response->dump();

    }
}
