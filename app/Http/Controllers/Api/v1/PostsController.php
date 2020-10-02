<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\v1\PostsServices;

class PostsController extends ApiRootController
{
    protected $PostsServices;

    public function __construct(PostsServices $postsServices)
    {
        $this->PostsServices = $postsServices;
    }

    // 리스트
    public function index(Int $page = 1) {
        $result = $this->PostsServices->posts($page);
        if(empty($result['posts'])) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }

    // 생성
    public function create(Request $request) {
        return Response::success($this->PostsServices->createPosts($request));
    }

    // 글 게시.
    public function publish(String $post_uuid) {
        $this->PostsServices->publishPosts($post_uuid);
        return Response::success_only_message();
    }

    // 글 정보(보기용).
    public function detail(String $slug_title) {
        return Response::success($this->PostsServices->detailPosts($slug_title));
    }

    // 글 정보(수정).
    public function edit(String $post_uuid) {
        return Response::success_only_data($this->PostsServices->editPosts($post_uuid));
    }

    // 업데이트.
    public function update(Request $request, String $post_uuid) {
        return Response::success($this->PostsServices->updatePosts($request, $post_uuid));
    }

    // 삭제.
    public function destroy(String $post_uuid) {

        $this->PostsServices->deletePosts($post_uuid);

        return Response::success_only_message();
    }
}
