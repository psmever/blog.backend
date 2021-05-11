<?php

namespace Tests\Feature\Controllers\Api\v1\SectionPostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ScribbleCreateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_scribble_post_create_라우터_오류()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/section-post');
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

    public function test_scribble_post_create_끄적끄적_로그인_하지_않은_상태에서_요청할때()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/section-post/scribble');
//        $response->dump();
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

    public function test_scribble_post_create_끄적끄적_본문_없을때()
    {
        $testBody = '{
            "contents" : {
                "html" : ""
                ,"text" : ""
            }
        }';
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', '/api/v1/section-post/scribble', json_decode($testBody, true));
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.contents_required')
            ]
        ]);
    }

    public function test_scribble_post_create_끄적끄적_정상_요청()
    {
        $testBody = '{
            "contents" : {
                "html" : "<h1>Blog.Frontend<\/h1>\n<h4>Git Clone.<\/h4>\n<pre><code>git clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n<\/code><\/pre>\n<h4>Config<\/h4>\n<pre><code>cp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n<\/code><\/pre>\n<h4>Node Module Install.<\/h4>\n<pre><code>yarn install\n<\/code><\/pre>\n<h3>Local Develper<\/h3>\n<pre><code>yarn start\nyarn start:prod\n<\/code><\/pre>\n<h3>Build<\/h3>\n<pre><code>yarn build\nyarn build:prod\n<\/code><\/pre>\n<h3>Server Deploy:prod<\/h3>\n<pre><code>yarn deploy:prod\n<\/code><\/pre>\n<h2>Contributing<\/h2>\n<p>Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.<\/p>\n<p>Please make sure to update tests as appropriate.<\/p>\n<h2>License<\/h2>\n<p><a href=\"https:\/\/choosealicense.com\/licenses\/mit\/\">MIT<\/a><\/p>"
                ,"text" : "## \uc774\ubbf8\uc9c0 \uc5c5\ub85c\ub4dc\n\n\n> \uc798 \ubcf4\uc774\ub098\uc694?\n\n\n![image](http:\/\/nicepage.media.test\/storage\/blog\/76c8f24c67684dccf2559fba407d164eace9b4da\/50475216-645d-41dc-b039-43272b15a6c1.jpeg)"
            }
        }';
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', '/api/v1/section-post/scribble', json_decode($testBody, true));
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message" ,
            "result" => [
                'post_uuid',
            ]
        ]);

        $post_uuid = $response['result']['post_uuid'];

        $this->assertDatabaseHas('section_posts', [
            'post_uuid' => $post_uuid,
            'publish' => 'Y',
            'active' => 'Y'
        ]);
    }
}
