<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\CustomException;
use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use App\Services\v1\SectionPostServices;
use Illuminate\Support\Facades\Response;

/**
 * Class SectionPostController
 * @package App\Http\Controllers\Api\v1
 */
class SectionPostController extends ApiRootController
{
    /**
     * @var SectionPostServices
     */
    protected $SectionPostServices;

    public function __construct(SectionPostServices $sectionPostServices)
    {
        $this->SectionPostServices = $sectionPostServices;
    }

    /**
     * 끄적 끄적 글 등록.
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function scribble_create(Request $request)
    {
        return Response::success($this->SectionPostServices->createPosts($request));
    }

    /**
     * 끄적 끄적 정보.
     * @return mixed
     */
    public function scribble_view()
    {
        return Response::success_only_data($this->SectionPostServices->viewPosts());
    }

    /**
     * 끄적 끄적 뷰카운트.
     * @param String $post_uuid
     * @return mixed
     * @throws CustomException
     */
    public function scribble_view_increment(String $post_uuid)
    {
        $this->SectionPostServices->incrementPostsViewCount($post_uuid);
        return Response::success_only_message();
    }

    /**
     * 불로그 소개 정보.
     * @return mixed
     */
    public function blog_view()
    {
        return Response::success_only_data($this->SectionPostServices->viewPosts());
    }

    /**
     * 불로그 소개 등록.
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function blog_create(Request $request)
    {
        return Response::success($this->SectionPostServices->createPosts($request));
    }

    /**
     * 블로그 소개 뷰카운트.
     * @param String $post_uuid
     * @return mixed
     * @throws CustomException
     */
    public function blog_view_increment(String $post_uuid)
    {
        $this->SectionPostServices->incrementPostsViewCount($post_uuid);
        return Response::success_only_message();
    }

    /**
     * 민군은 소개 정보.
     * @return mixed
     */
    public function mingun_view()
    {
        return Response::success_only_data($this->SectionPostServices->viewPosts());
    }

    /**
     * 민군은 소개 글등록.
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function mingun_create(Request $request)
    {
        return Response::success($this->SectionPostServices->createPosts($request));
    }

    /**
     * 민군은 소개 글등록.
     * @param String $post_uuid
     * @return mixed
     * @throws CustomException
     */
    public function mingun_view_increment(String $post_uuid)
    {
        $this->SectionPostServices->incrementPostsViewCount($post_uuid);
        return Response::success_only_message();
    }

}
