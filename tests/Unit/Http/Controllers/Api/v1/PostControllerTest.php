<?php

namespace Tests\Unit\Http\Controllers\Api\v1;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    public $testNormalHeader;
    public $testAbnormalHeader;

    public function setUp(): void
    {
        parent::setUp();

        $this->testNormalHeader = $this->getTestAccessTokenHeader();
        $this->testAbnormalHeader = $this->getTestApiHeaders();
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

    // TODO POST 글등록 api 테스트
    // 로그인이 되어 있지 않을떄.
    public function test_post_create_로그인_하지_않은_상태에서_요청할때()
    {
        $response = $this->withHeaders($this->testAbnormalHeader)->json('POST', '/api/v1/post');
        // $response->dump();
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.login.unauthorized')
            ]
        ]);
    }
    // 제목이 없을떄.
    public function test_post_create_제목없이_요청_할때()
    {
        $this->assertTrue(true);
    }
    // 테그가 없을때.
    // 본문이 없을떄.

}
