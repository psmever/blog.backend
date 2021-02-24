<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class DetailTest extends TestCase
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

    public function test_post_detail_포스트_보기_등록되어있지_않은_포스트_요청()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/sdafsdfasdf/detail', []);
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

    public function test_post_detail_포스트_보기_비공개_포스트_요청()
    {
        $testPost = \App\Models\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Models\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_active' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_active' => 'N'
        ]);

        $testSlugTitle = $testPost->slug_title;

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testSlugTitle}/detail", []);
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

    public function test_post_detail_포스트_보기_개시전_포스트_요청()
    {
        $testPost = \App\Models\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Models\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_publish' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'N'
        ]);

        $testSlugTitle = $testPost->slug_title;

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testSlugTitle}/detail", []);
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

    public function test_post_detail_포스트_보기_정상_포스트_요청()
    {
        $randPost = \App\Models\Posts::select("slug_title")->where([['post_active', 'Y'], ['post_publish', 'Y']])->inRandomOrder()->first();
        $testSlugTitle = $randPost->slug_title;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testSlugTitle}/detail", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "post_uuid",
            "user" => [
                "user_uuid",
                "user_type" => [
                    "code_id",
                    "code_name"
                ],
                "user_level" => [
                    "code_id",
                    "code_name"
                ],
                "name",
                "nickname",
                "email",
                "active"
            ],
            "post_title",
            "slug_title",
            "contents_html",
            "contents_text",
            "markdown",
            "tags" => [
                '*' => [
                    "id",
                    "text"
                ],
            ],
            "view_count",
            "detail_created",
            "detail_updated"
        ]);
    }
}
