<?php

namespace Tests\Feature\Controllers\Api\v1\SectionPostController;

use App\Models\SectionPosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SectionTotalHistoryTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // 글 리스트 테스트
    public function test_section_post_total_history_list_데이터_없을때()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', 'api/v1/section-post/S07010/history-list-total/1', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_section_post_total_history_list_정상_요청()
    {
        SectionPosts::factory(10)->create();

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', 'api/v1/section-post/S07010/history-list-total/1', []);
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
                    "publish",
                    "active",
                    "display_flag",
                    "created_time"
                ],
            ]
        ]);
    }
}
