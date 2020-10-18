<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class WaitingPostTest extends TestCase
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

    public function test_waiting_결과_없을때()
    {
        \App\Model\Posts::where('id', '>' , 0)->update([
            'post_publish' => 'Y'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/waiting-list', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_waiting_정상()
    {

    }
}
