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
    // TODO: 2020-09-17 00:35 리스트.페이징 형식으로.
    public function index(Request $request) {
        return Response::success($this->PostsServices->posts($request));
    }

    // 생성
    public function create(Request $request) {
        return Response::success($this->PostsServices->createPosts($request));
    }

    // 글 정보(뷰).
    public function view($slug_title) {
        return Response::success($this->PostsServices->viewPosts($slug_title));
    }

    // 글 정보(수정).
    public function edit(Request $request) {
        return Response::success();
    }

    // 업데이트.
    public function update(Request $request) {
        return Response::success();
    }

    // 삭제.
    public function destroy(Request $request) {
        return Response::success();
    }
}
