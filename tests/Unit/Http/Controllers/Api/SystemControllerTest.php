<?php

namespace Tests\Unit\Http\Controllers\Api;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class SystemControllerTest extends TestCase
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

    // api SystemControllerTest
    public function test_server_check_status()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check/status');
        // $response->dump();
        $response->assertStatus(204);
    }

    // 시스템 공지 사항 테스트 Start

    public function test_server_notice_not_exists()
    {
        Storage::disk('sitedata')->delete('notice.txt');
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check/notice');
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_server_notice_not_exists_contents()
    {
        Storage::disk('sitedata')->put('notice.txt', '');
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check/notice');
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_server_notice_exists_contents()
    {
        $tmpNoticeMessage = '긴급 공지 사항 테스트입니다.';
        Storage::disk('sitedata')->put('notice.txt', $tmpNoticeMessage);
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/system/check/notice');
        // $response->dump();
        $response->assertOk();
        $response->assertJsonStructure(
            $this->getDefaultSuccessJsonType()
        )->assertJsonFragment([
            "notice_message" => $tmpNoticeMessage
        ]);
    }
    // 시스템 공지 사항 테스트 End

    // public function test_server_check_base_data() { }
}
