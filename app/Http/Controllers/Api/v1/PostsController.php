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
        return Response::success_only_data($this->PostsServices->detailPosts($slug_title));
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

    // 이미지 등록.
    public function create_image(Request $request) {
        return Response::success_only_data($this->PostsServices->createImage($request));
    }

    // 뷰카운트
    public function view_increment(String $post_uuid) {
        $this->PostsServices->incrementPostsViewCount($post_uuid);

        return Response::success_only_message();
    }

    // 검색
    public function search(String $search_item) {

        $result = $this->PostsServices->postsSearch($search_item);

        if(empty($result)) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }

    // 테그 그룹 리스트.
    public function tag_list() {
        return Response::success_only_data($this->PostsServices->postsTagList());
    }

    // 테그 검색 리스트.
    public function tag_search(String $search_item) {
        $result = $this->PostsServices->postsTagItemSearch($search_item);

        if(empty($result)) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }
}
