<?php

namespace Tests\Feature\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class WaitingPostTest extends TestCase
{
    public function test_waiting_결과_없을때()
    {
        \App\Models\Posts::where('id', '>' , 0)->update([
            'post_publish' => 'Y'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/write/waiting-list', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_waiting_정상()
    {
        \App\Models\Posts::where('id', '>' , 0)->update([
            'post_publish' => 'N'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/write/waiting-list', []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                "post_uuid",
                "post_title"
            ]
        ]);
    }
}
