<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use App\Services\v1\SpecialtyServices;
use Illuminate\Http\Request;

use App\Services\v1\SectionPostServices;
use Illuminate\Support\Facades\Response;

class SectionPostController extends ApiRootController
{
    protected $SectionPostServices;

    public function __construct(SpecialtyServices $sectionPostServices)
    {
        $this->SectionPostServices = $sectionPostServices;
    }

    /**
     * 끄적 끄적 정보.
     * @return mixed
     */
    public function scribble_view()
    {
        return Response::success_only_data([]);
    }

    /**
     * 끄적 끄적 글 등록.
     * @return mixed
     */
    public function scribble_create()
    {
        return Response::success_only_data([]);
    }

    /**
     * 불로그 소개 정보.
     * @return mixed
     */
    public function blogs_view()
    {
        return Response::success_only_data([]);
    }

    /**
     * 불로그 소개 등록.
     * @return mixed
     */
    public function blogs_create()
    {
        return Response::success_only_data([]);
    }

    /**
     * 주인장 소개 정보.
     * @return mixed
     */
    public function mingun_view()
    {
        return Response::success_only_data([]);
    }

    /**
     * 주인장 소개 글등록.
     * @return mixed
     */
    public function mingun_create()
    {
        return Response::success_only_data([]);
    }

}
