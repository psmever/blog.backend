<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;

class UpdateTest extends TestCase
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


    // 글 업데이트 테스트
    public function test_post_update_로그인_하지_않은_상태에서_요청할때()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', "/api/v1/post/${testPostUuid}/update");
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

    public function test_post_update_존재_하지않은_요청_할때()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/1111111111111${testPostUuid}/update");
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

    public function test_post_update_등록자와_다를때()
    {
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/update", []);
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

    public function test_post_update_제목_없이_요청_할때()
    {
        $testBody = '{
            "title":"",
            "tags":[
                {
                    "tag_id":"Html","tag_text":"Html"
                }
                ,{
                    "tag_id":"Markdown","tag_text":"Markdown"
                }
                ,{
                    "tag_id":"Code","tag_text":"Code"
                }
            ],
            "contents" : {
                "html" : "<h1>Blog.Frontend<\/h1>\n<h4>Git Clone.<\/h4>\n<pre><code>git clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n<\/code><\/pre>\n<h4>Config<\/h4>\n<pre><code>cp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n<\/code><\/pre>\n<h4>Node Module Install.<\/h4>\n<pre><code>yarn install\n<\/code><\/pre>\n<h3>Local Develper<\/h3>\n<pre><code>yarn start\nyarn start:prod\n<\/code><\/pre>\n<h3>Build<\/h3>\n<pre><code>yarn build\nyarn build:prod\n<\/code><\/pre>\n<h3>Server Deploy:prod<\/h3>\n<pre><code>yarn deploy:prod\n<\/code><\/pre>\n<h2>Contributing<\/h2>\n<p>Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.<\/p>\n<p>Please make sure to update tests as appropriate.<\/p>\n<h2>License<\/h2>\n<p><a href=\"https:\/\/choosealicense.com\/licenses\/mit\/\">MIT<\/a><\/p>"
                ,"text" : "# Blog.Frontend\n\n\n#### Git Clone.\n\n```\ngit clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n```\n\n#### Config\n```\ncp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n```\n\n#### Node Module Install.\n```\nyarn install\n```\n\n### Local Develper\n\n```\nyarn start\nyarn start:prod\n```\n\n### Build\n```\nyarn build\nyarn build:prod\n```\n\n### Server Deploy:prod\n```\nyarn deploy:prod\n```\n\n\n## Contributing\nPull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.\n\nPlease make sure to update tests as appropriate.\n\n## License\n[MIT](https:\/\/choosealicense.com\/licenses\/mit\/)"
            }
        }';

        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/update", json_decode($testBody, true));
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.title_required')
            ]
        ]);

    }

    public function test_post_update_테그_없이_요청_할때()
    {
        $testBody = '{
            "title":"blog.front Readme.MD",
            "tags":"",
            "contents" : {
                "html" : "<h1>Blog.Frontend<\/h1>\n<h4>Git Clone.<\/h4>\n<pre><code>git clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n<\/code><\/pre>\n<h4>Config<\/h4>\n<pre><code>cp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n<\/code><\/pre>\n<h4>Node Module Install.<\/h4>\n<pre><code>yarn install\n<\/code><\/pre>\n<h3>Local Develper<\/h3>\n<pre><code>yarn start\nyarn start:prod\n<\/code><\/pre>\n<h3>Build<\/h3>\n<pre><code>yarn build\nyarn build:prod\n<\/code><\/pre>\n<h3>Server Deploy:prod<\/h3>\n<pre><code>yarn deploy:prod\n<\/code><\/pre>\n<h2>Contributing<\/h2>\n<p>Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.<\/p>\n<p>Please make sure to update tests as appropriate.<\/p>\n<h2>License<\/h2>\n<p><a href=\"https:\/\/choosealicense.com\/licenses\/mit\/\">MIT<\/a><\/p>"
                ,"text" : "# Blog.Frontend\n\n\n#### Git Clone.\n\n```\ngit clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n```\n\n#### Config\n```\ncp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n```\n\n#### Node Module Install.\n```\nyarn install\n```\n\n### Local Develper\n\n```\nyarn start\nyarn start:prod\n```\n\n### Build\n```\nyarn build\nyarn build:prod\n```\n\n### Server Deploy:prod\n```\nyarn deploy:prod\n```\n\n\n## Contributing\nPull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.\n\nPlease make sure to update tests as appropriate.\n\n## License\n[MIT](https:\/\/choosealicense.com\/licenses\/mit\/)"
                }
        }';
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/update", json_decode($testBody, true));
        // $response->dump();
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.tags_required')
            ]
        ]);
    }

    public function test_post_update_내용_없이_요청_할때()
    {
        $testBody = '{
            "title":"blog.front Readme.MD",
            "tags":[
                {
                    "tag_id":"Html","tag_text":"Html"
                }
                ,{
                    "tag_id":"Markdown","tag_text":"Markdown"
                }
                ,{
                    "tag_id":"Code","tag_text":"Code"
                }
            ],
            "contents" : {
                "html" : ""
                ,"text" : ""
            }
        }';
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/update", json_decode($testBody, true));
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

    public function test_post_update_정상_요청_할때()
    {
        $testBody = '{
            "title":"테스트 포스트 입니다.",
            "tags":[
                {
                    "tag_id":"Html","tag_text":"Html"
                }
                ,{
                    "tag_id":"Markdown","tag_text":"Markdown"
                }
                ,{
                    "tag_id":"Code","tag_text":"Code"
                }
            ],
            "contents" : {
                "html" : "<h1>Blog.Frontend<\/h1>\n<h4>Git Clone.<\/h4>\n<pre><code>git clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n<\/code><\/pre>\n<h4>Config<\/h4>\n<pre><code>cp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n<\/code><\/pre>\n<h4>Node Module Install.<\/h4>\n<pre><code>yarn install\n<\/code><\/pre>\n<h3>Local Develper<\/h3>\n<pre><code>yarn start\nyarn start:prod\n<\/code><\/pre>\n<h3>Build<\/h3>\n<pre><code>yarn build\nyarn build:prod\n<\/code><\/pre>\n<h3>Server Deploy:prod<\/h3>\n<pre><code>yarn deploy:prod\n<\/code><\/pre>\n<h2>Contributing<\/h2>\n<p>Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.<\/p>\n<p>Please make sure to update tests as appropriate.<\/p>\n<h2>License<\/h2>\n<p><a href=\"https:\/\/choosealicense.com\/licenses\/mit\/\">MIT<\/a><\/p>"
                ,"text" : "# Blog.Frontend\n\n\n#### Git Clone.\n\n```\ngit clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n```\n\n#### Config\n```\ncp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n```\n\n#### Node Module Install.\n```\nyarn install\n```\n\n### Local Develper\n\n```\nyarn start\nyarn start:prod\n```\n\n### Build\n```\nyarn build\nyarn build:prod\n```\n\n### Server Deploy:prod\n```\nyarn deploy:prod\n```\n\n\n## Contributing\nPull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.\n\nPlease make sure to update tests as appropriate.\n\n## License\n[MIT](https:\/\/choosealicense.com\/licenses\/mit\/)"
            }
        }';
        $randPost = \App\Models\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/update", json_decode($testBody, true));
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message" ,
            "result" => [
                'post_uuid',
                'slug_title',
            ]
        ]);
    }
}
