<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class PublishTest extends TestCase
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

    // 퍼블리시 테스트.
    public function test_post_publish_로그인_하지_않은_상태에서_요청할때()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', "/api/v1/post/${testPostUuid}/publish");
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
    public function test_post_publish_존재_하지않은_요청_할때()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/1111111111111${testPostUuid}/publish");
        // $response->dump();
        $response->assertStatus(406);
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
    public function test_post_publish_등록자와_다를때()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/publish", []);
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
    public function test_post_publish_정상처리()
    {
        $testPost = \App\Models\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Models\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_publish' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'N'
        ]);

        $testPostUuid = $testPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/publish", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('default.server.result_success')
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'Y'
        ]);
    }
}
