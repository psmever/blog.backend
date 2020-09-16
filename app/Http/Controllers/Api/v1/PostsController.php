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
    public function index(Request $request) {
        return Response::success();
    }

    // 생성
    public function create(Request $request) {
        return Response::success($this->PostsServices->createPosts($request));
    }

    // 정보.
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
