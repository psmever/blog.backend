<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class TagTest extends TestCase
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

    public function test_tag_리스트() {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/tag/tag-list', []);
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
            ],
        ]);
    }

    public function test_tag_결과_리스트_없을때()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/tag/eourqiweurpoasdfkjhaksldjhfweqoriuqqwe/tag-search', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_tag_검색_정상()
    {
        $randPost = \App\Model\PostsTags::select("tag_text")->inRandomOrder()->first();
        $tag_text = $randPost->tag_text;

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/tag/${tag_text}/tag-search', []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'per_page',
            'current_page',
            'hasmore',
            'posts' => [
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
                ],
            ]
        ]);
    }
}
