<?php

namespace Tests\Feature\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class ListTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    // 글 리스트 테스트
    public function test_post_list_포스트_리스트_요청_테스트_없을떄()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/100000', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_post_list_포스트_리스트_테스트()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post', []);
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
