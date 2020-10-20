<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    // 검색어 없이 요청 할떄.
    public function test_post_search_검색_검색어_없이_요청()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post//search', []);
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
    // 결과가 없을때.
    public function test_post_search_검색_결과가_없을때()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/search', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    // 정상 요청 일때.
    public function test_post_search_검색_정상()
    {
        $randPost = \App\Models\Posts::select("title")->inRandomOrder()->first();
        $post_title = $randPost->title;

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${post_title}/search", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                "post_id",
                "post_uuid",
                "user" => [
                    "user_uuid",
                    "name",
                    "nickname",
                    "email",
                ],
                "post_title",
                "slug_title",
                "list_contents",
                "markdown",
                "tags" => [
                    '*' => [
                        "tag_id",
                        "tag_text"
                    ]
                ],
                "thumb_url",
                "view_count",
                "post_active",
                "post_publish",
                "list_created"
            ]
        ]);
    }
}
