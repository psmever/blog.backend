<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\Auth\AuthTokenPair;
use App\Services\AuthService;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class AuthController extends ApiBaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Issue an access & refresh token pair.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $tokens = $this->authService->login($credentials['email'], $credentials['password']);
        if (! $tokens) {
            return $this->responseUnauthorized('이메일 또는 비밀번호가 올바르지 않습니다.');
        }

        return $this->responseSuccess($this->formatTokenPair($tokens));
    }

    /**
     * Return the authenticated user's data.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        return $this->responseSuccess([
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Revoke the current access token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $this->authService->logout($user);

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->responseSuccess(['success' => true]);
    }

    /**
     * Revoke all tokens for the user.
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $this->authService->logoutAll($user);

        return $this->responseSuccess(['success' => true]);
    }

    /**
     * Rotate tokens using a valid refresh token.
     */
    public function refresh(Request $request)
    {
        $payload = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $result = $this->authService->refresh($payload['refresh_token']);

        if (! $result->successful() || ! $result->tokens) {
            return $this->responseUnauthorized($result->failureMessage ?? '유효하지 않은 리프레시 토큰입니다.');
        }

        return $this->responseSuccess($this->formatTokenPair($result->tokens), 'Token refreshed');
    }

    private function formatTokenPair(AuthTokenPair $tokens): array
    {
        return [
            'token_type' => 'Bearer',
            'access_token' => $tokens->accessToken->plainTextToken,
            'access_token_expires_at' => $this->formatExpiry($tokens->accessToken->accessToken->expires_at),
            'refresh_token' => $tokens->refreshToken->plainTextToken,
            'refresh_token_expires_at' => $this->formatExpiry($tokens->refreshToken->accessToken->expires_at),
            'user' => $this->formatUser($tokens->user),
        ];
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    private function formatExpiry(?CarbonInterface $expiresAt): ?string
    {
        return $this->formatDateTimeForResponse($expiresAt);
    }
}
