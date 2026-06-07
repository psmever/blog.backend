<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\User;
use App\Services\ShortUrlService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShortUrlController extends ApiBaseController
{
    public function __construct(
        private readonly ShortUrlService $shortUrls
    ) {}

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $payload = $request->validate([
            'original_url' => ['required', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $shortUrl = $this->shortUrls->create(
            (string) $payload['original_url'],
            isset($payload['expires_at']) ? (string) $payload['expires_at'] : null,
            $user
        );

        return $this->responseSuccess(
            $this->shortUrls->format($shortUrl),
            '정상 처리되었습니다',
            Response::HTTP_CREATED
        );
    }

    public function show(string $code)
    {
        $shortUrl = $this->shortUrls->findActiveByCode($code);
        if (! $shortUrl) {
            return $this->responseNotFound('단축 URL을 찾을 수 없습니다.');
        }

        return $this->responseSuccess($this->shortUrls->format($shortUrl));
    }
}
