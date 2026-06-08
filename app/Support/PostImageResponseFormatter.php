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
     * @return array<string, mixed>
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
                'body_image' => null,
                'thumbnail' => null,
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
            'body_image' => $this->formatBodyImage($image),
            'thumbnail' => $this->formatThumbnail($image),
        ];
    }

    /**
     * @return array<string, int|string>|null
     */
    private function formatBodyImage(PostImage $image): ?array
    {
        $bodyImage = $image->bodyVariant;
        if (! $bodyImage) {
            return null;
        }

        return [
            'url' => $this->postImageService->urlForVariant($bodyImage),
            'width' => $bodyImage->width,
            'height' => $bodyImage->height,
            'size' => $bodyImage->size,
            'mime_type' => $bodyImage->mime_type,
        ];
    }

    /**
     * @return array<string, int|string>|null
     */
    private function formatThumbnail(PostImage $image): ?array
    {
        $thumbnail = $image->thumbnailVariant;
        if (! $thumbnail) {
            return null;
        }

        return [
            'url' => $this->postImageService->urlForVariant($thumbnail),
            'width' => $thumbnail->width,
            'height' => $thumbnail->height,
            'size' => $thumbnail->size,
            'mime_type' => $thumbnail->mime_type,
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
