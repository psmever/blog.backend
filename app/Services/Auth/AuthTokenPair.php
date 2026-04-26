<?php

namespace App\Services\Auth;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

final readonly class AuthTokenPair
{
    public function __construct(
        public User $user,
        public NewAccessToken $accessToken,
        public NewAccessToken $refreshToken
    ) {}
}
