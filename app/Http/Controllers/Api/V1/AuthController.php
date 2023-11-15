<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ClientErrorException;
use App\Http\Controllers\Controller;
use App\Http\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Response;

class AuthController extends Controller
{
	/**
	 * @var AuthService
	 */
	protected AuthService $authService;

	/**
	 * @param AuthService $authService
	 */
	function __construct(AuthService $authService)
	{
		$this->authService = $authService;
	}

	/**
	 * 로그인
	 * @throws ClientErrorException
	 */
	public function Login(): JsonResponse
	{
		return Response::SuccessMacro($this->authService->LoginAttempt());
	}

	/**
	 * 토큰 리프레쉬
	 * @return JsonResponse
	 */
	public function RefreshToken(): JsonResponse
	{
		return Response::SuccessMacro($this->authService->RefreshTokenAttempt());
	}

	/**
	 * 토큰 정보
	 * @return JsonResponse
	 */
	public function TokenInfo(): JsonResponse
	{
		return Response::SuccessMacro($this->authService->TokenInfoAttempt());
	}

}
