<?php

namespace Tests\Unit\Http\Controllers\Api\v1\PostsController;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadTest extends TestCase
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
}
