<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\ClientErrorException;
use App\Exceptions\CustomException;
use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\AuthServices;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api\v1
 */
class AuthController extends ApiRootController
{
    /**
     * @var AuthServices
     */
    protected AuthServices $AuthServices;

    /**
     * AuthController constructor.
     * @param AuthServices $authServices
     */
    public function __construct(AuthServices $authServices)
    {
        $this->AuthServices = $authServices;
    }

    /**
     * 로그인
     *
     * @return mixed
     * @throws CustomException
     * @throws ServerErrorException
     */
    public function client_login()
    {
        $task = $this->AuthServices->attemptLogin();

        return Response::success_only_data($task);
    }

    /**
     * 로그아웃
     *
     * @param Request $request
     * @return mixed
     */
    public function client_logout(Request $request)
    {
        $request->user()->token()->revoke();

        return Response::success_no_content();
    }

    /**
     * 로그인 체크 및 로그인 사용자 정보.
     *
     * @return mixed
     */
    public function client_login_check()
    {
        return Response::success_only_data($this->AuthServices->attemptLoginCheck());
    }

    /**
     * 로그인 사용자 토큰 새로고침.
     *
     * @return mixed
     * @throws CustomException
     * @throws ServerErrorException
     * @throws ClientErrorException
     */
    public function client_token_refresh()
    {
        return Response::success_only_data($this->AuthServices->attemptLoginRefreshToken());
    }
}
