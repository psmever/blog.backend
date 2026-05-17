<?php

namespace App\Repositories;

use App\Models\User;
use DateTimeInterface;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

interface PersonalAccessTokenRepositoryInterface
{
    public function findByPlainTextToken(string $plainTextToken): ?PersonalAccessToken;

    /**
     * @param  array<int, string>  $abilities
     */
    public function createForUser(
        User $user,
        string $name,
        array $abilities,
        ?DateTimeInterface $expiresAt = null
    ): NewAccessToken;

    public function delete(PersonalAccessToken $token): void;

    public function deleteAllForUser(User $user): int;

    public function deleteAccessTokensForUser(User $user, string $ability): int;
}
