<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class EditTest extends TestCase
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


    // 글 수정 ( edit )
    // 존재 하지 요청
    public function test_post_edit_post_uuid_없이_요청()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', "/api/v1/post//edit", []);
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
    // 로그인 안한 상태.
    public function test_post_edit_로그인_하지_않은_상태()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', "/api/v1/post/asdasd/edit", []);
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
    // 없는 글일때
    public function test_post_edit_등록_되지않은_글_요청()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/11111111111111111${testPostUuid}/edit", []);
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
    // 권한 없을때.
    public function test_post_edit_권한_부족()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('GET', "/api/v1/post/${testPostUuid}/edit", []);
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

    // 정상.
    public function test_post_edit_정상_처리()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testPostUuid}/edit", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "post_id",
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
                    "tag_id",
                    "tag_text"
                ],
            ],
            "post_active",
            "post_publish",
            "created",
            "updated"
        ]);
    }

}
