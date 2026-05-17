<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\PersonalAccessTokenRepositoryInterface;
use DateTimeInterface;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class EloquentPersonalAccessTokenRepository implements PersonalAccessTokenRepositoryInterface
{
    public function findByPlainTextToken(string $plainTextToken): ?PersonalAccessToken
    {
        return PersonalAccessToken::findToken($plainTextToken);
    }

    public function createForUser(
        User $user,
        string $name,
        array $abilities,
        ?DateTimeInterface $expiresAt = null
    ): NewAccessToken {
        return $user->createToken(
            name: $name,
            abilities: $abilities,
            expiresAt: $expiresAt
        );
    }

    public function delete(PersonalAccessToken $token): void
    {
        $token->delete();
    }

    public function deleteAllForUser(User $user): int
    {
        return $user->tokens()->delete();
    }

    public function deleteAccessTokensForUser(User $user, string $ability): int
    {
        return PersonalAccessToken::query()
            ->where('tokenable_type', $user->getMorphClass())
            ->where('tokenable_id', $user->getKey())
            ->whereJsonContains('abilities', $ability)
            ->delete();
    }
}
