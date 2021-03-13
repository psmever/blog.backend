<?php

namespace Tests\Unit\Http\Controllers\Api\v1\SectionPostController;

use App\Models\SectionPosts;
use Tests\TestCase;

class BlogViewCountTest extends TestCase
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

    public function test_blog_post_view_count_라우터_오류()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', '/api/v1/section-post/blog/11111111111111111111111111111111');
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
    public function test_blog_post_view_count_요청_에러()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', "/api/v1/section-post/blog/687bfad3-4500-4dcc-b3cf-99070f272182/view-increment", []);
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
    public function test_blog_post_view_count_정상_요청_일때()
    {
        SectionPosts::factory()->create();
        $testPost = SectionPosts::latest()->first();
        SectionPosts::where('post_uuid', $testPost->post_uuid)->update([
            'gubun' => 'S07020'
        ]);
//
        $this->assertDatabaseHas('section_posts', [
            'post_uuid' => $testPost->post_uuid,
            'gubun' => "S07020",
            'publish' => 'Y',
            'active' => 'Y'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/section-post/blog/{$testPost->post_uuid}/view-increment", []);
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
