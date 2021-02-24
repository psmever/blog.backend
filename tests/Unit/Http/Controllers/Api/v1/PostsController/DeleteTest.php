<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class DeleteTest extends TestCase
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

    // 글 삭제.
    public function test_post_delete_post_uuid_없이_요청(){
        $response = $this->withHeaders($this->getTestApiHeaders())->json('DELETE', "/api/v1/post//destroy", []);
        // $response->dump();
        $response->assertStatus(404);
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
    public function test_post_delete_로그인_안한_상태(){
        $response = $this->withHeaders($this->getTestApiHeaders())->json('DELETE', "/api/v1/post/asdasd/destroy", []);
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
    public function test_post_delete_등록_되지않은_글_요청(){
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('DELETE', "/api/v1/post/11111111111111111${testPostUuid}/destroy", []);
        // $response->dump();
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.exception.model_not_found_exception')
            ]
        ]);
    }

    public function test_post_delete_권한_부족(){
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('DELETE', "/api/v1/post/${testPostUuid}/destroy", []);
        // $response->dump();
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.exception.forbidden_error_exception')
            ]
        ]);
    }

    public function test_post_delete_정상_처리(){
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('DELETE', "/api/v1/post/${testPostUuid}/destroy", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('default.server.result_success')
        ]);
    }
}
