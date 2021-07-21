<?php

namespace Tests\Feature\Controllers\Api\v1\SectionPostController;

use App\Models\SectionPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionHistoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    // 글 리스트 테스트
    public function test_section_post_history_데이터_없을때()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/section-post/S07010/history', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_section_post_history_정상()
    {
        SectionPosts::factory(10)->create();
        SectionPosts::where('id', '>', 0)->update([
            'display_flag' => 'Y'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/section-post/S07010/history', []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'per_page',
            'current_page',
            'hasmore',
            'historys' => [
                '*' => [
                    "post_uuid",
                    "gubun" => [
                        "code_id",
                        "code_name"
                    ],
                    "smal_content",
                    "created_at",
                    "created_time"
                ],
            ]
        ]);
    }

    public function test_section_post_view_history_등록되어있지_않은_포스트_요청()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/section-post/S07010/aaaaaaaaaaaaaaaaaaaaaaa/history', []);
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

    public function test_section_post_view_history_비공개_포스트_요청()
    {
        SectionPosts::factory(10)->create();
        SectionPosts::where('id', '>', 0)->update([
            'display_flag' => 'N'
        ]);

        $testPost = SectionPosts::select("post_uuid")->where('gubun', 'S07010')->inRandomOrder()->first();
        $post_uuid = $testPost['post_uuid'];

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/section-post/S07010/{$post_uuid}/history", []);
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

    public function test_section_post_view_history_정상_요청()
    {
        SectionPosts::factory(10)->create();
        SectionPosts::where('id', '>', 0)->update([
            'display_flag' => 'Y'
        ]);

        $testPost = SectionPosts::select("post_uuid")->where([['gubun', 'S07010'], ['display_flag', 'Y']])->inRandomOrder()->first();
        $post_uuid = $testPost['post_uuid'];

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/section-post/S07010/{$post_uuid}/history", []);
//        $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "post_uuid",
            "contents_html",
            "contents_text",
            "markdown",
            "created"
        ]);
    }
}
