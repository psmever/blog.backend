<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;

class AuthController extends ApiBaseController
{
    private const ACCESS_TOKEN_NAME = 'api-access';

    private const REFRESH_TOKEN_NAME = 'api-refresh';

    private const ACCESS_TOKEN_ABILITY = 'access-api';

    private const REFRESH_TOKEN_ABILITY = 'token:refresh';

    private const ACCESS_TOKEN_TTL_HOURS = 2;

    private const REFRESH_TOKEN_TTL_DAYS = 30;

    /**
     * Issue an access & refresh token pair.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->responseUnauthorized('이메일 또는 비밀번호가 올바르지 않습니다.');
        }

        $user->tokens()->delete();

        $access = $user->createToken(
            name: self::ACCESS_TOKEN_NAME,
            abilities: [self::ACCESS_TOKEN_ABILITY],
            expiresAt: now()->addHours(self::ACCESS_TOKEN_TTL_HOURS)
        );

        $refresh = $user->createToken(
            name: self::REFRESH_TOKEN_NAME,
            abilities: [self::REFRESH_TOKEN_ABILITY],
            expiresAt: now()->addDays(self::REFRESH_TOKEN_TTL_DAYS)
        );

        return $this->responseSuccess([
            'token_type' => 'Bearer',
            'access_token' => $access->plainTextToken,
            'access_token_expires_at' => $this->formatExpiry($access->accessToken->expires_at),
            'refresh_token' => $refresh->plainTextToken,
            'refresh_token_expires_at' => $this->formatExpiry($refresh->accessToken->expires_at),
            'user' => $this->formatUser($user),
        ]);
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

        $token = $user->currentAccessToken();
        /** @var PersonalAccessToken|TransientToken|null $token */
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

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

        $user->tokens()->delete();

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

        $storedToken = PersonalAccessToken::findToken($payload['refresh_token']);

        if (! $storedToken || ! $storedToken->can(self::REFRESH_TOKEN_ABILITY)) {
            return $this->responseUnauthorized('유효하지 않은 리프레시 토큰입니다.');
        }

        if ($storedToken->expires_at && $storedToken->expires_at->isPast()) {
            $storedToken->delete();

            return $this->responseUnauthorized('만료된 리프레시 토큰입니다.');
        }

        $user = $storedToken->tokenable;

        if (! $user instanceof User) {
            $storedToken->delete();

            return $this->responseUnauthorized('유효하지 않은 리프레시 토큰입니다.');
        }

        PersonalAccessToken::query()
            ->where('tokenable_type', $user->getMorphClass())
            ->where('tokenable_id', $user->getKey())
            ->whereJsonContains('abilities', self::ACCESS_TOKEN_ABILITY)
            ->delete();

        $storedToken->delete();

        $access = $user->createToken(
            name: self::ACCESS_TOKEN_NAME,
            abilities: [self::ACCESS_TOKEN_ABILITY],
            expiresAt: now()->addHours(self::ACCESS_TOKEN_TTL_HOURS)
        );

        $refresh = $user->createToken(
            name: self::REFRESH_TOKEN_NAME,
            abilities: [self::REFRESH_TOKEN_ABILITY],
            expiresAt: now()->addDays(self::REFRESH_TOKEN_TTL_DAYS)
        );

        return $this->responseSuccess([
            'token_type' => 'Bearer',
            'access_token' => $access->plainTextToken,
            'access_token_expires_at' => $this->formatExpiry($access->accessToken->expires_at),
            'refresh_token' => $refresh->plainTextToken,
            'refresh_token_expires_at' => $this->formatExpiry($refresh->accessToken->expires_at),
            'user' => $this->formatUser($user),
        ], 'Token refreshed');
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
