<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PersonalAccessTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Services\Auth\AuthTokenPair;
use App\Services\Auth\RefreshTokenResult;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    private const ACCESS_TOKEN_NAME = 'api-access';

    private const REFRESH_TOKEN_NAME = 'api-refresh';

    private const ACCESS_TOKEN_ABILITY = 'access-api';

    private const REFRESH_TOKEN_ABILITY = 'token:refresh';

    private const ACCESS_TOKEN_TTL_HOURS = 2;

    private const REFRESH_TOKEN_TTL_DAYS = 30;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly PersonalAccessTokenRepositoryInterface $tokens
    ) {}

    public function login(string $email, string $password): ?AuthTokenPair
    {
        $user = $this->users->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $this->tokens->deleteAllForUser($user);

        return $this->issueTokenPair($user);
    }

    public function logout(User $user): void
    {
        /** @var mixed $token */
        $token = $user->currentAccessToken();

        if (! $token instanceof PersonalAccessToken) {
            return;
        }

        $this->tokens->delete($token);
    }

    public function logoutAll(User $user): void
    {
        $this->tokens->deleteAllForUser($user);
    }

    public function refresh(string $plainTextRefreshToken): RefreshTokenResult
    {
        $storedToken = $this->tokens->findByPlainTextToken($plainTextRefreshToken);

        if (! $storedToken || ! $storedToken->can(self::REFRESH_TOKEN_ABILITY)) {
            return RefreshTokenResult::invalid();
        }

        if ($storedToken->expires_at && $storedToken->expires_at->isPast()) {
            $this->tokens->delete($storedToken);

            return RefreshTokenResult::expired();
        }

        $user = $storedToken->tokenable;

        if (! $user instanceof User) {
            $this->tokens->delete($storedToken);

            return RefreshTokenResult::invalid();
        }

        $this->tokens->deleteAccessTokensForUser($user, self::ACCESS_TOKEN_ABILITY);
        $this->tokens->delete($storedToken);

        return RefreshTokenResult::success($this->issueTokenPair($user));
    }

    private function issueTokenPair(User $user): AuthTokenPair
    {
        $access = $this->tokens->createForUser(
            $user,
            self::ACCESS_TOKEN_NAME,
            [self::ACCESS_TOKEN_ABILITY],
            now()->addHours(self::ACCESS_TOKEN_TTL_HOURS)
        );

        $refresh = $this->tokens->createForUser(
            $user,
            self::REFRESH_TOKEN_NAME,
            [self::REFRESH_TOKEN_ABILITY],
            now()->addDays(self::REFRESH_TOKEN_TTL_DAYS)
        );

        return new AuthTokenPair($user, $access, $refresh);
    }
}
