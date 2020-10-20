<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class ViewCountTest extends TestCase
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


    // 뷰카운트.
    public function test_post_viewcount_포스트_뷰카운트_등록되어있지_않은_포스트_요청()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', '/api/v1/post/sdafsdfasdf/view-increment', []);
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

    public function test_post_viewcount_포스트_뷰카운트_비공개_포스트_요청()
    {
        $testPost = \App\Models\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Models\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_active' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_active' => 'N'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/$testPost->post_uuid/view-increment", []);
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

    public function test_post_viewcount_포스트_뷰카운트_개시전_포스트_요청()
    {
        $testPost = \App\Models\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Models\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_publish' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'N'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/$testPost->post_uuid/view-increment", []);
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

    public function test_post_viewcount_포스트_뷰카운트_정상_포스트_요청()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->where([['post_active', 'Y'], ['post_publish', 'Y']])->inRandomOrder()->first();
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/$randPost->post_uuid/view-increment", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('default.server.result_success')
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $randPost->post_uuid,
            'post_active' => 'Y',
            'post_publish' => 'Y',
            'view_count' => 1
        ]);
    }
}
