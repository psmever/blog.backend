<?php

namespace Tests\Feature\Controllers\Api\v1\SectionPostController;

use App\Models\SectionPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionManageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_section_manage_테스트()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_section_manage_라우터_오류()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', '/api/v1/section-post/manage//hidden');
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

    public function test_section_manage_데이터_없을때()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/section-post/manage/asdasdasdasdasdasdasd/hidden");
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

    public function test_section_manage_hidden_처리()
    {
        SectionPosts::factory(10)->create();
        $testPost = SectionPosts::all()->toArray();

        $getPost_uuid = $testPost[7]['post_uuid'];

        SectionPosts::where('post_uuid', $getPost_uuid)->update([
            'display_flag' => 'Y'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/section-post/manage/{$getPost_uuid}/hidden");
//        $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message"
        ]);


        $this->assertDatabaseHas('section_posts', [
            'post_uuid' => $getPost_uuid,
            'display_flag' => 'N'
        ]);
    }

    public function test_section_manage_display_처리()
    {
        SectionPosts::factory(10)->create();
        $testPost = SectionPosts::all()->toArray();

        $getPost_uuid = $testPost[7]['post_uuid'];

        SectionPosts::where('post_uuid', $getPost_uuid)->update([
            'display_flag' => 'N'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/section-post/manage/{$getPost_uuid}/display");
//        $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message"
        ]);


        $this->assertDatabaseHas('section_posts', [
            'post_uuid' => $getPost_uuid,
            'display_flag' => 'Y'
        ]);
    }
}
