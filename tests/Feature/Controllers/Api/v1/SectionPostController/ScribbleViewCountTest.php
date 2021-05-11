<?php

namespace Tests\Feature\Controllers\Api\v1\SectionPostController;

use App\Models\SectionPosts;
use Tests\TestCase;

class ScribbleViewCountTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_scribble_post_view_count_라우터_오류()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', '/api/v1/section-post/scribble/11111111111111111111111111111111');
//        $response->dump();
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

    // 잘못된 요청.
    public function test_scribble_post_view_count_요청_에러()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', "/api/v1/section-post/scribble/687bfad3-4500-4dcc-b3cf-99070f272182/view-increment", []);
//        $response->dump();
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

    // 정상 요청.
    public function test_scribble_post_view_count_정상_요청_일때()
    {
        SectionPosts::factory()->create();
        $testPost = SectionPosts::latest()->first();
        SectionPosts::where('post_uuid', $testPost->post_uuid)->update([
            'gubun' => 'S07010'
        ]);
//
        $this->assertDatabaseHas('section_posts', [
            'post_uuid' => $testPost->post_uuid,
            'gubun' => "S07010",
            'publish' => 'Y',
            'active' => 'Y'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/section-post/scribble/{$testPost->post_uuid}/view-increment", []);
//        $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message"
        ]);

        $this->assertDatabaseHas('section_posts', [
            'post_uuid' => $testPost->post_uuid,
            'publish' => 'Y',
            'active' => 'Y',
            'view_count' => ($testPost->view_count + 1)
        ]);
    }
}
