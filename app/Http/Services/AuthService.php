<?php

namespace App\Http\Services;

use App\Enums\TokenAbility;
use App\Exceptions\ClientErrorException;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class AuthService
{
	/**
	 * @var Request
	 */
	protected Request $currentRequest;

	function __construct(Request $currentRequest)
	{
		$this->currentRequest = $currentRequest;

	}

	/**
	 * @throws ClientErrorException
	 */
	public function LoginAttempt(): array
	{
		$validator = Validator::make($this->currentRequest->all(), [
			'email' => 'required|email|exists:users,email',
			'password' => 'required',
		],
			[
				'email.required' => __('validator.email-required'),
				'email.email' => __('validator.email-email'),
				'email.exists' => __('validator.email-exists'),
				'password.required' => __('validator.password-required'),
			]);

		if ($validator->fails()) {
			throw new ClientErrorException($validator->errors()->first());
		}

		if (!Auth::attempt(['email' => $this->currentRequest->input('email'), 'password' => $this->currentRequest->input('password')])) {
			throw new ClientErrorException(__('response.auth-password-attempt'));
		}

		$accessToken = $this->currentRequest->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
		$refreshToken = $this->currentRequest->user()->createToken('refresh_token', [TokenAbility::REFRESH_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')))->plainTextToken;

		return [
			'access_token' => $accessToken,
			'refresh_token' => $refreshToken,
		];
	}

	/**
	 * 토큰 리프레쉬
	 * @return array
	 */
	public function RefreshTokenAttempt(): array
	{
		$accessToken = $this->currentRequest->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.expiration')))->plainTextToken;

		return [
			'access_token' => $accessToken,
		];
	}

	/**
	 * 토큰 사용자 정보
	 * @return array
	 */
	public function TokenInfoAttempt(): array
	{
		return [
			'uuid' => $this->currentRequest->user()->uuid,
			'level' => $this->currentRequest->user()->level
		];
	}
}
