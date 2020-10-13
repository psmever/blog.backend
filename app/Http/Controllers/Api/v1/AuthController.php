<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

use App\Services\v1\AuthServices;

class AuthController extends ApiRootController
{
    protected $AuthServices;

    public function __construct(AuthServices $authServices)
    {
        $this->AuthServices = $authServices;
    }

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
     * FIXME passport 를 이용 로그아웃 처리.
     *
     * @return void
     */
    public function client_logout(Request $request)
    {
        $request->user()->token()->revoke();

        return Response::success_no_content();
    }

    /**
     * 로그인 체크 및 로그인 사용자 정보.
     *
     * @return void
     */
    public function client_login_check()
    {
        return Response::success_only_data($this->AuthServices->attemptLoginCheck());
    }

    /**
     * 로그인 사용자 토큰 새로고침.
     *
     * @return void
     */
    public function client_token_refresh()
    {
        return Response::success_only_data($this->AuthServices->attemptLoginRefreshToken());
    }
}
