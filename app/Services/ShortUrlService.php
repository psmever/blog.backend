<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ShortUrlService
{
    private const SHORT_URL_PREFIX = '/s/';

    private const CODE_LENGTH = 6;

    public function create(string $originalUrl, ?string $expiresAt, User $user): ShortUrl
    {
        $normalizedUrl = $this->normalizeOriginalUrl($originalUrl);
        $expiresAtValue = $this->normalizeExpiresAt($expiresAt);

        $existing = ShortUrl::query()
            ->where('original_url', $normalizedUrl)
            ->first();

        if ($existing instanceof ShortUrl) {
            return $existing;
        }

        return ShortUrl::query()->create([
            'code' => $this->makeUniqueCode(),
            'original_url' => $normalizedUrl,
            'created_by' => $user->getKey(),
            'expires_at' => $expiresAtValue,
        ]);
    }

    public function findActiveByCode(string $code): ?ShortUrl
    {
        return ShortUrl::query()
            ->where('code', $code)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function resolveShortUrl(string $url): ?ShortUrl
    {
        if (! str_starts_with($url, self::SHORT_URL_PREFIX)) {
            return null;
        }

        $code = substr($url, strlen(self::SHORT_URL_PREFIX));
        if ($code === '' || str_contains($code, '/')) {
            return null;
        }

        return $this->findActiveByCode($code);
    }

    public function markAccessed(ShortUrl $shortUrl): ShortUrl
    {
        $shortUrl->forceFill([
            'click_count' => ((int) $shortUrl->click_count) + 1,
            'last_accessed_at' => now(),
        ])->save();

        return $shortUrl->refresh();
    }

    /**
     * @return array{code: string, short_url: string, original_url: string}
     */
    public function format(ShortUrl $shortUrl): array
    {
        return [
            'code' => (string) $shortUrl->code,
            'short_url' => self::SHORT_URL_PREFIX.$shortUrl->code,
            'original_url' => (string) $shortUrl->original_url,
        ];
    }

    public function normalizeOriginalUrl(string $originalUrl): string
    {
        $url = trim($originalUrl);

        if (
            $url === ''
            || ! str_starts_with($url, '/')
            || str_starts_with($url, '//')
            || preg_match('/^[a-z][a-z0-9+\-.]*:/i', $url) === 1
            || str_starts_with($url, self::SHORT_URL_PREFIX)
        ) {
            throw new ApiException('original_url 값이 올바르지 않습니다.', 422, [
                'original_url' => ['프런트 내부 경로만 사용할 수 있습니다.'],
            ]);
        }

        return $url;
    }

    private function normalizeExpiresAt(?string $expiresAt): ?Carbon
    {
        if ($expiresAt === null || trim($expiresAt) === '') {
            return null;
        }

        return Carbon::parse($expiresAt);
    }

    private function makeUniqueCode(): string
    {
        do {
            $code = Str::random(self::CODE_LENGTH);
        } while (ShortUrl::query()->where('code', $code)->exists());

        return $code;
    }
}
