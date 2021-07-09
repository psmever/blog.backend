<?php

namespace Tests\Feature\Controllers\Api\v1\SectionPostController;

use App\Models\SectionPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogViewTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_blog_post_view_라우터_오류()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', '/api/v1/section-post');
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

    public function test_blog_post_view_데이터_없을때()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/section-post/blog", []);
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

    public function test_blog_post_view_정상_요청()
    {

        SectionPosts::factory()->create();
        $testPost = SectionPosts::latest()->first()->id;
        SectionPosts::find($testPost)->update([
            'gubun' => 'S07020'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/section-post/blog", []);
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