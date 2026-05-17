<?php

namespace App\Services\Auth;

final readonly class RefreshTokenResult
{
    private function __construct(
        public ?AuthTokenPair $tokens,
        public ?string $failureMessage
    ) {}

    public static function success(AuthTokenPair $tokens): self
    {
        return new self($tokens, null);
    }

    public static function invalid(): self
    {
        return new self(null, '유효하지 않은 리프레시 토큰입니다.');
    }

    public static function expired(): self
    {
        return new self(null, '만료된 리프레시 토큰입니다.');
    }

    public function successful(): bool
    {
        return $this->tokens !== null;
    }
}
