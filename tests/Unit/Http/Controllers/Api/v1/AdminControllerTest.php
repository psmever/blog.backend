<?php

namespace Tests\Unit\Http\Controllers\Api\v1;

use Tests\TestCase;

class AdminControllerTest extends TestCase
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

    // TODO 2020-08-27 23:49 로그인 유닛 테스트.
    public function test_admin_login()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/admin/login');
        $response->assertOk();
    }
}

