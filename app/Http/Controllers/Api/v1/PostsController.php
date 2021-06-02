<?php


namespace App\Http\Controllers\Api\v1;

use App\Exceptions\CustomException;
use App\Exceptions\ForbiddenErrorException;
use App\Exceptions\SomethingErrorException;
use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\PostsServices;

class PostsController extends ApiRootController
{
    protected PostsServices $PostsServices;

    public function __construct(PostsServices $postsServices)
    {
        $this->PostsServices = $postsServices;
    }

    /**
     * 리스트
     *
     * @param Int $page
     * @return mixed
     */
    public function index(Int $page = 1) {
        $result = $this->PostsServices->posts($page);
        if(empty($result['posts'])) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }

    /**
     * 생성
     *
     * @throws CustomException
     */
    public function create(Request $request) {
        return Response::success($this->PostsServices->createPosts($request));
    }


    /**
     * 글 게시.
     *
     * @param String $post_uuid
     * @return mixed
     * @throws ForbiddenErrorException
     */
    public function publish(String $post_uuid) {
        $this->PostsServices->publishPosts($post_uuid);
        return Response::success_only_message();
    }

    /**
     * 글 숨김처리.
     *
     * @param String $post_uuid
     * @return mixed
     * @throws ForbiddenErrorException
     */
    public function hide(String $post_uuid) {
        $this->PostsServices->hidePosts($post_uuid);
        return Response::success_only_message();
    }

    /**
     * 글 정보(보기용).
     *
     * @param String $slug_title
     * @return mixed
     */
    public function detail(String $slug_title) {
        return Response::success_only_data($this->PostsServices->detailPosts($slug_title));
    }

    /**
     * 글 정보(수정).
     *
     * @param String $post_uuid
     * @return mixed
     * @throws ForbiddenErrorException
     */
    public function edit(String $post_uuid) {
        return Response::success_only_data($this->PostsServices->editPosts($post_uuid));
    }

    /**
     * 업데이트.
     *
     * @param Request $request
     * @param String $post_uuid
     * @return mixed
     * @throws CustomException
     * @throws ForbiddenErrorException
     */
    public function update(Request $request, String $post_uuid) {
        return Response::success($this->PostsServices->updatePosts($request, $post_uuid));
    }

    /**
     * 삭제.
     *
     * @param String $post_uuid
     * @return mixed
     * @throws ForbiddenErrorException
     */
    public function destroy(String $post_uuid) {

        $this->PostsServices->deletePosts($post_uuid);

        return Response::success_only_message();
    }

    /**
     * 이미지 등록.
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * @throws SomethingErrorException
     */
    public function create_image(Request $request) {
        return Response::success_only_data($this->PostsServices->createImage($request));
    }

    /**
     * 뷰카운트
     *
     * @param String $post_uuid
     * @return mixed
     * @throws CustomException
     */
    public function view_increment(String $post_uuid) {
        $this->PostsServices->incrementPostsViewCount($post_uuid);

        return Response::success_only_message();
    }

    /**
     * 검색
     *
     * @param String $search_item
     * @return mixed
     */
    public function search(String $search_item) {

        $result = $this->PostsServices->postsSearch($search_item);

        if(empty($result)) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }

    /**
     * 테그 그룹 리스트.
     *
     * @return mixed
     */
    public function tag_list() {
        return Response::success_only_data($this->PostsServices->postsTagList());
    }

    /**
     * 테그 검색 리스트.
     *
     * @param String $search_item
     * @return mixed
     */
    public function tag_search(String $search_item) {
        $result = $this->PostsServices->postsTagItemSearch($search_item);

        if(empty($result)) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }

    /**
     * 글 개시전 글 리스트
     *
     * @return mixed
     */
    public function waiting_list() {
        $result = $this->PostsServices->waitingPostsList();

        if(empty($result)) {
            return Response::success_no_content();
        } else {
            return Response::success_only_data($result);
        }
    }
}
