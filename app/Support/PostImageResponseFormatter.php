<?php

namespace App\Support;

use App\Models\PostImage;
use App\Services\PostImageService;

class PostImageResponseFormatter
{
    public function __construct(
        private readonly PostImageService $postImageService
    ) {}

    /**
     * @return array<string, bool|int|string|null>
     */
    public function format(?PostImage $image): array
    {
        if (! $image) {
            return [
                'uuid' => null,
                'purpose' => 'default',
                'url' => $this->defaultCoverImageUrl(),
                'width' => (int) config('posts.default_cover_image.width', 1200),
                'height' => (int) config('posts.default_cover_image.height', 630),
                'size' => (int) config('posts.default_cover_image.size', 0),
                'is_default' => true,
            ];
        }

        return [
            'uuid' => $image->uuid,
            'purpose' => $image->purpose,
            'url' => $this->postImageService->urlForImage($image),
            'width' => $image->width,
            'height' => $image->height,
            'size' => $image->size,
            'is_default' => false,
        ];
    }

    private function defaultCoverImageUrl(): string
    {
        $url = (string) config('posts.default_cover_image.url', '/images/default-cover.png');
        if ($url === '') {
            $url = '/images/default-cover.png';
        }

        return $this->postImageService->responseUrl($url);
    }
}
