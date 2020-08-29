<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\v1\AuthServices;

class AuthController extends ApiRootController
{
    protected $AuthServices;

    public function __construct(AuthServices $authServices)
    {
        $this->AuthServices = $authServices;
    }

    // TODO 2020-08-27 22:54  관리자 기능

    /**
     * 로그인
     *
     * @return void
     */
    public function client_login(Request $request)
    {
        $task = $this->AuthServices->attemptLogin();

        return Response::success_only_data($task);
    }

    /**
     * 로그아웃
     *
     * @return void
     */
    public function client_logout()
    {
        return Response::success();
    }
}
