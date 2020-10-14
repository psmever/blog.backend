<?php

namespace Tests\Unit\Http\Controllers\Api\v1;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostsControllerTest extends TestCase
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

    // 로그인이 되어 있지 않을떄.
    public function test_post_create_로그인_하지_않은_상태에서_요청할때()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', '/api/v1/post');
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

    // 제목이 없을떄.
    public function test_post_create_제목_없이_요청_할때()
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
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', '/api/v1/post', json_decode($testBody, true));
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

    public function test_post_create_테그_없이_요청_할때()
    {
        $testBody = '{
            "title":"blog.front Readme.MD",
            "tags":"",
            "contents" : {
                "html" : "<h1>Blog.Frontend<\/h1>\n<h4>Git Clone.<\/h4>\n<pre><code>git clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n<\/code><\/pre>\n<h4>Config<\/h4>\n<pre><code>cp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n<\/code><\/pre>\n<h4>Node Module Install.<\/h4>\n<pre><code>yarn install\n<\/code><\/pre>\n<h3>Local Develper<\/h3>\n<pre><code>yarn start\nyarn start:prod\n<\/code><\/pre>\n<h3>Build<\/h3>\n<pre><code>yarn build\nyarn build:prod\n<\/code><\/pre>\n<h3>Server Deploy:prod<\/h3>\n<pre><code>yarn deploy:prod\n<\/code><\/pre>\n<h2>Contributing<\/h2>\n<p>Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.<\/p>\n<p>Please make sure to update tests as appropriate.<\/p>\n<h2>License<\/h2>\n<p><a href=\"https:\/\/choosealicense.com\/licenses\/mit\/\">MIT<\/a><\/p>"
                ,"text" : "# Blog.Frontend\n\n\n#### Git Clone.\n\n```\ngit clone https:\/\/github.com\/psmever\/blog.front.git blog.front\n```\n\n#### Config\n```\ncp config\/sample.environment.env config\/development.env\ncp config\/sample.environment.env config\/production.env\n```\n\n#### Node Module Install.\n```\nyarn install\n```\n\n### Local Develper\n\n```\nyarn start\nyarn start:prod\n```\n\n### Build\n```\nyarn build\nyarn build:prod\n```\n\n### Server Deploy:prod\n```\nyarn deploy:prod\n```\n\n\n## Contributing\nPull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.\n\nPlease make sure to update tests as appropriate.\n\n## License\n[MIT](https:\/\/choosealicense.com\/licenses\/mit\/)"
                }
        }';
        // print_r(json_decode($testBody, true));
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', '/api/v1/post', json_decode($testBody, true));
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

    // 본문이 없을떄.
    public function test_post_create_내용_없이_요청_할때()
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
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', '/api/v1/post', json_decode($testBody, true));
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

    public function test_post_create_정상_요청_할때()
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
                ,"text" : "## \uc774\ubbf8\uc9c0 \uc5c5\ub85c\ub4dc\n\n\n> \uc798 \ubcf4\uc774\ub098\uc694?\n\n\n![image](http:\/\/nicepage.media.test\/storage\/blog\/76c8f24c67684dccf2559fba407d164eace9b4da\/50475216-645d-41dc-b039-43272b15a6c1.jpeg)"
            }
        }';
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', '/api/v1/post', json_decode($testBody, true));
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message" ,
            "result" => [
                'post_uuid',
                'slug_title',
            ]
        ]);

        $post_uuid = $response['result']['post_uuid'];

        $this->assertDatabaseHas('posts', [
            'post_uuid' => $post_uuid,
            'post_publish' => 'N',
            'post_active' => 'Y'
        ]);
    }

    // 퍼블리시 테스트.
    public function test_post_publish_로그인_하지_않은_상태에서_요청할때()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestApiHeaders())->json('PUT', "/api/v1/post/${testPostUuid}/publish");
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
    public function test_post_publish_존재_하지않은_요청_할때()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/1111111111111${testPostUuid}/publish");
        // $response->dump();
        $response->assertStatus(406);
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
    public function test_post_publish_등록자와_다를때()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/publish", []);
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
    public function test_post_publish_정상처리()
    {
        $testPost = \App\Model\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Model\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_publish' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'N'
        ]);

        $testPostUuid = $testPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/${testPostUuid}/publish", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('default.server.result_success')
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'Y'
        ]);
    }

    // 글 리스트 테스트
    public function test_포스트_리스트_요청_테스트_없을떄()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/100000', []);
        // $response->dump();
        $response->assertStatus(204);
    }

    public function test_포스트_리스트_테스트()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post', []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'per_page',
            'current_page',
            'posts' => [
                '*' => [
                    "post_id",
                    "post_uuid",
                    "user" => [
                        "user_uuid",
                        "name",
                        "nickname",
                        "email",
                    ],
                    "post_title",
                    "slug_title",
                    "list_contents",
                    "markdown",
                    "tags" => [
                        '*' => [
                            "tag_id",
                            "tag_text"
                        ]
                    ],
                    "thumb_url",
                    "view_count",
                    "post_active",
                    "post_publish",
                    "list_created"
                ],
            ]
        ]);
    }

    public function test_포스트_보기_등록되어있지_않은_포스트_요청()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', '/api/v1/post/sdafsdfasdf/detail', []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_포스트_보기_비공개_포스트_요청()
    {
        $testPost = \App\Model\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Model\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_active' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_active' => 'N'
        ]);

        $testSlugTitle = $testPost->slug_title;

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testSlugTitle}/detail", []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_포스트_보기_개시전_포스트_요청()
    {
        $testPost = \App\Model\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Model\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_publish' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'N'
        ]);

        $testSlugTitle = $testPost->slug_title;

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testSlugTitle}/detail", []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_포스트_보기_정상_포스트_요청()
    {
        $randPost = \App\Model\Posts::select("slug_title")->where([['post_active', 'Y'], ['post_publish', 'Y']])->inRandomOrder()->first();
        $testSlugTitle = $randPost->slug_title;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testSlugTitle}/detail", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "post_uuid",
            "user" => [
                "user_uuid",
                "user_type" => [
                    "code_id",
                    "code_name"
                ],
                "user_level" => [
                    "code_id",
                    "code_name"
                ],
                "name",
                "nickname",
                "email",
                "active"
            ],
            "post_title",
            "slug_title",
            "contents_html",
            "contents_text",
            "markdown",
            "tags" => [
                '*' => [
                    "tag_id",
                    "tag_text"
                ],
            ],
            "view_count",
            "detail_created",
            "detail_updated"
        ]);
    }

    // 글 업데이트 테스트
    public function test_post_update_로그인_하지_않은_상태에서_요청할때()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
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
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/1111111111111${testPostUuid}/update");
        // $response->dump();
        $response->assertStatus(406);
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
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
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

        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
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
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
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
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
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
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
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

    // 글 삭제.
    public function test_post_delete_post_uuid_없이_요청(){
        $response = $this->withHeaders($this->getTestApiHeaders())->json('DELETE', "/api/v1/post//destroy", []);
        // $response->dump();
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
    public function test_post_delete_로그인_안한_상태(){
        $response = $this->withHeaders($this->getTestApiHeaders())->json('DELETE', "/api/v1/post/asdasd/destroy", []);
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
    public function test_post_delete_등록_되지않은_글_요청(){
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('DELETE', "/api/v1/post/11111111111111111${testPostUuid}/destroy", []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_post_delete_권한_부족(){
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('DELETE', "/api/v1/post/${testPostUuid}/destroy", []);
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

    public function test_post_delete_정상_처리(){
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('DELETE', "/api/v1/post/${testPostUuid}/destroy", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('default.server.result_success')
        ]);
    }

    // 글 수정 ( edit )
    // 존재 하지 요청
    public function test_post_edit_post_uuid_없이_요청()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', "/api/v1/post//edit", []);
        // $response->dump();
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
    // 로그인 안한 상태.
    public function test_post_edit_로그인_하지_않은_상태()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('GET', "/api/v1/post/asdasd/edit", []);
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
    // 없는 글일때
    public function test_post_edit_등록_되지않은_글_요청()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/11111111111111111${testPostUuid}/edit", []);
        // $response->dump();
        $response->assertStatus(406);
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
    // 권한 없을때.
    public function test_post_edit_권한_부족()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestGuestAccessTokenHeader())->json('GET', "/api/v1/post/${testPostUuid}/edit", []);
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

    // 정상.
    public function test_post_edit_정상_처리()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->inRandomOrder()->first();
        $testPostUuid = $randPost->post_uuid;
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('GET', "/api/v1/post/${testPostUuid}/edit", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "post_id",
            "post_uuid",
            "user" => [
                "user_uuid",
                "user_type" => [
                    "code_id",
                    "code_name"
                ],
                "user_level" => [
                    "code_id",
                    "code_name"
                ],
                "name",
                "nickname",
                "email",
                "active"
            ],
            "post_title",
            "slug_title",
            "contents_html",
            "contents_text",
            "markdown",
            "tags" => [
                '*' => [
                    "tag_id",
                    "tag_text"
                ],
            ],
            "post_active",
            "post_publish",
            "created",
            "updated"
        ]);
    }

    // 이미지
    public function test_post_image_update_로그인_안되어_있을떄() {
        $response = $this->withHeaders($this->getTestApiHeaders())->json('POST', "/api/v1/post/create-image", []);
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
    public function test_post_image_update_이미지_없이_요청() {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', "/api/v1/post/create-image", []);
        // $response->dump();
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.image_required')
            ]
        ]);
    }
    public function test_post_image_update_이미지_형식이_올바르지_않을때() {
        $file = UploadedFile::fake()->create('document.pdf', 300, 'application/pdf');
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', "/api/v1/post/create-image", [
            'image' => $file
        ]);
        // $response->dump();
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.image_image')
            ]
        ]);
    }
    public function test_post_image_update_이미지_정상적인_이미지가_아닐때() {
        $file = UploadedFile::fake()->create('document.pdf', 300, 'image/png1');
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', "/api/v1/post/create-image", [
            'image' => $file
        ]);
        // $response->dump();
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.image_image')
            ]
        ]);
    }
    // FIXME 2020-10-12 17:21  이건 어떻게 처리를 해야 하는지?
    // public function test_post_image_update_이미지_업로드가_불가한_이미지() {
    //     $file = UploadedFile::fake()->create('document.pdf', 300, 'image/WBMP');
    //     $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', "/api/v1/post/create-image", [
    //         'image' => $file
    //     ]);
    //     $response->dump();
    //     $response->assertJsonStructure([
    //         'error' => [
    //             'error_message'
    //         ]
    //     ])->assertJsonFragment([
    //         'error' => [
    //             'error_message' => __('default.post.image_mimes')
    //         ]
    //     ]);
    // }
    public function test_post_image_update_이미지_용량이_큰이미지() {
        $file = UploadedFile::fake()->create('test_image.jpeg', 30000000, 'image/jpeg');
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', "/api/v1/post/create-image", [
            'image' => $file
        ]);
        // $response->dump();
        $response->assertJsonStructure([
            'error' => [
                'error_message'
            ]
        ])->assertJsonFragment([
            'error' => [
                'error_message' => __('default.post.image_max')
            ]
        ]);
    }
    // FIXME 2020-10-12 17:27 굳이 정상 테스트를 해야 하는지;;
    // public function test_post_image_update_이미지_정상() {

    //     $file = UploadedFile::fake()->create('test_image.jpeg', 400, 'image/jpeg');
    //     $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('POST', "/api/v1/post/create-image", [
    //         'image' => $file
    //     ]);
    //     $response->dump();
    //     $response->assertStatus(200);
    //     $response->assertJsonStructure([
    //         "media_url",
    //     ]);
    // }

    // 뷰카운트.
    public function test_포스트_뷰카운트_등록되어있지_않은_포스트_요청()
    {
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', '/api/v1/post/sdafsdfasdf/view-increment', []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_포스트_뷰카운트_비공개_포스트_요청()
    {
        $testPost = \App\Model\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Model\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_active' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_active' => 'N'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/$testPost->post_uuid/view-increment", []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_포스트_뷰카운트_개시전_포스트_요청()
    {
        $testPost = \App\Model\Posts::select("post_uuid", "slug_title")->inRandomOrder()->first();

        \App\Model\Posts::where('post_uuid', $testPost->post_uuid)->update([
            'post_publish' => 'N'
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $testPost->post_uuid,
            'post_publish' => 'N'
        ]);

        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/$testPost->post_uuid/view-increment", []);
        // $response->dump();
        $response->assertStatus(406);
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

    public function test_포스트_뷰카운트_정상_포스트_요청()
    {
        $randPost = \App\Model\Posts::select("post_uuid")->where([['post_active', 'Y'], ['post_publish', 'Y']])->inRandomOrder()->first();
        $response = $this->withHeaders($this->getTestAccessTokenHeader())->json('PUT', "/api/v1/post/$randPost->post_uuid/view-increment", []);
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => __('default.server.result_success')
        ]);

        $this->assertDatabaseHas('posts', [
            'post_uuid' =>  $randPost->post_uuid,
            'post_active' => 'Y',
            'post_publish' => 'Y',
            'view_count' => 1
        ]);
    }
}
