<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\PostImage;
use App\Models\PostImageVariant;
use GdImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class PostImageThumbnailService
{
    public function createForImage(PostImage $image, ?string $sourceBytes = null, ?string $sourcePath = null): PostImageVariant
    {
        $existing = $image->thumbnailVariant()->first();
        if ($existing) {
            return $existing;
        }

        $this->ensureSupported();

        $path = sprintf(
            'posts/%s/%s/%s.webp',
            $image->post_uuid,
            PostImageVariant::VARIANT_THUMBNAIL,
            $image->uuid
        );

        try {
            $sourceBytes ??= Storage::disk($image->disk)->get($image->path);
            $thumbnailBytes = $this->render($sourceBytes, $sourcePath);
            $stored = Storage::disk($image->disk)->put(
                $path,
                $thumbnailBytes,
                $this->storageWriteOptions($image->disk)
            );

            if (! $stored || ! Storage::disk($image->disk)->exists($path)) {
                throw new RuntimeException('Thumbnail storage write returned without a persisted file.');
            }

            $variant = $image->variants()->create([
                'variant' => PostImageVariant::VARIANT_THUMBNAIL,
                'disk' => $image->disk,
                'path' => $path,
                'url' => $this->normalizePublicUrlPath(Storage::disk($image->disk)->url($path)),
                'mime_type' => 'image/webp',
                'size' => strlen($thumbnailBytes),
                'width' => $this->width(),
                'height' => $this->height(),
            ]);

            $image->setRelation('thumbnailVariant', $variant);

            return $variant;
        } catch (Throwable $e) {
            $this->deleteQuietly($image->disk, $path);

            Log::error('Post image thumbnail generation failed.', [
                'post_image_id' => $image->getKey(),
                'disk' => $image->disk,
                'source_path' => $image->path,
                'thumbnail_path' => $path,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);

            if ($e instanceof ApiException) {
                throw $e;
            }

            throw new ApiException('이미지 썸네일 생성에 실패했습니다.', 500);
        }
    }

    public function createBodyForImage(PostImage $image, ?string $sourceBytes = null, ?string $sourcePath = null, bool $force = false): PostImageVariant
    {
        $existing = $image->bodyVariant()->first();
        if ($existing && ! $force) {
            return $existing;
        }

        $this->ensureSupported();

        $path = sprintf(
            'posts/%s/body-resized/%s.webp',
            $image->post_uuid,
            $image->uuid
        );

        try {
            $sourceBytes ??= Storage::disk($image->disk)->get($image->path);
            [$bodyBytes, $width, $height] = $this->renderBody($sourceBytes, $sourcePath);
            $stored = Storage::disk($image->disk)->put(
                $path,
                $bodyBytes,
                $this->storageWriteOptions($image->disk)
            );

            if (! $stored || ! Storage::disk($image->disk)->exists($path)) {
                throw new RuntimeException('Body image storage write returned without a persisted file.');
            }

            $attributes = [
                'disk' => $image->disk,
                'path' => $path,
                'url' => $this->normalizePublicUrlPath(Storage::disk($image->disk)->url($path)),
                'mime_type' => 'image/webp',
                'size' => strlen($bodyBytes),
                'width' => $width,
                'height' => $height,
            ];

            if ($existing) {
                $existing->fill($attributes);
                $existing->save();
                $variant = $existing->refresh();
            } else {
                $variant = $image->variants()->create([
                    'variant' => PostImageVariant::VARIANT_BODY,
                    ...$attributes,
                ]);
            }

            $image->setRelation('bodyVariant', $variant);

            return $variant;
        } catch (Throwable $e) {
            $this->deleteQuietly($image->disk, $path);

            Log::error('Post body image generation failed.', [
                'post_image_id' => $image->getKey(),
                'disk' => $image->disk,
                'source_path' => $image->path,
                'body_path' => $path,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);

            if ($e instanceof ApiException) {
                throw $e;
            }

            throw new ApiException('본문 이미지 리사이즈에 실패했습니다.', 500);
        }
    }

    public function ensureSupported(): void
    {
        $gd = function_exists('gd_info') ? gd_info() : [];

        if (
            ! extension_loaded('gd')
            || ! function_exists('imagecreatefromstring')
            || ! function_exists('imagecreatetruecolor')
            || ! function_exists('imagecopyresampled')
            || ! function_exists('imagewebp')
            || ! ($gd['JPEG Support'] ?? false)
            || ! ($gd['PNG Support'] ?? false)
            || ! ($gd['GIF Read Support'] ?? false)
            || ! ($gd['WebP Support'] ?? false)
        ) {
            throw new ApiException('이미지 썸네일 생성을 사용할 수 없습니다. GD JPEG, PNG, GIF, WebP 지원이 필요합니다.', 500);
        }
    }

    private function render(string $sourceBytes, ?string $sourcePath): string
    {
        $source = @imagecreatefromstring($sourceBytes);
        if (! $source instanceof GdImage) {
            throw new RuntimeException('Unsupported image data.');
        }

        try {
            $source = $this->orientJpeg($source, $sourcePath);
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $targetWidth = $this->width();
            $targetHeight = $this->height();
            $scale = max($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
            $cropWidth = (int) round($targetWidth / $scale);
            $cropHeight = (int) round($targetHeight / $scale);
            $sourceX = max(0, (int) floor(($sourceWidth - $cropWidth) / 2));
            $sourceY = max(0, (int) floor(($sourceHeight - $cropHeight) / 2));

            $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
            if (! $thumbnail instanceof GdImage) {
                throw new RuntimeException('Failed to allocate thumbnail image.');
            }

            try {
                imagecopyresampled(
                    $thumbnail,
                    $source,
                    0,
                    0,
                    $sourceX,
                    $sourceY,
                    $targetWidth,
                    $targetHeight,
                    $cropWidth,
                    $cropHeight
                );

                ob_start();
                $written = imagewebp($thumbnail, null, $this->quality());
                $bytes = ob_get_clean();

                if (! $written || ! is_string($bytes) || $bytes === '') {
                    throw new RuntimeException('Failed to encode WebP thumbnail.');
                }

                return $bytes;
            } finally {
                imagedestroy($thumbnail);
            }
        } finally {
            imagedestroy($source);
        }
    }

    /**
     * @return array{0: string, 1: int, 2: int}
     */
    private function renderBody(string $sourceBytes, ?string $sourcePath): array
    {
        $source = @imagecreatefromstring($sourceBytes);
        if (! $source instanceof GdImage) {
            throw new RuntimeException('Unsupported image data.');
        }

        try {
            $source = $this->orientJpeg($source, $sourcePath);
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $maxWidth = $this->bodyMaxWidth();
            $scale = min(1, $maxWidth / $sourceWidth);
            $targetWidth = max(1, (int) round($sourceWidth * $scale));
            $targetHeight = max(1, (int) round($sourceHeight * $scale));

            $body = imagecreatetruecolor($targetWidth, $targetHeight);
            if (! $body instanceof GdImage) {
                throw new RuntimeException('Failed to allocate body image.');
            }

            try {
                imagealphablending($body, false);
                imagesavealpha($body, true);
                $transparent = imagecolorallocatealpha($body, 0, 0, 0, 127);
                if ($transparent !== false) {
                    imagefill($body, 0, 0, $transparent);
                }

                imagecopyresampled(
                    $body,
                    $source,
                    0,
                    0,
                    0,
                    0,
                    $targetWidth,
                    $targetHeight,
                    $sourceWidth,
                    $sourceHeight
                );

                ob_start();
                $written = imagewebp($body, null, $this->bodyQuality());
                $bytes = ob_get_clean();

                if (! $written || ! is_string($bytes) || $bytes === '') {
                    throw new RuntimeException('Failed to encode WebP body image.');
                }

                return [$bytes, $targetWidth, $targetHeight];
            } finally {
                imagedestroy($body);
            }
        } finally {
            imagedestroy($source);
        }
    }

    private function orientJpeg(GdImage $source, ?string $sourcePath): GdImage
    {
        if ($sourcePath === null || ! function_exists('exif_read_data')) {
            return $source;
        }

        $exif = @exif_read_data($sourcePath);
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;
        $angle = match ($orientation) {
            3 => 180,
            5, 6 => -90,
            7, 8 => 90,
            default => 0,
        };
        $flip = match ($orientation) {
            2, 5, 7 => IMG_FLIP_HORIZONTAL,
            4 => IMG_FLIP_VERTICAL,
            default => null,
        };

        if ($angle !== 0) {
            $rotated = imagerotate($source, $angle, 0);
            if ($rotated instanceof GdImage) {
                imagedestroy($source);
                $source = $rotated;
            }
        }

        if ($flip !== null) {
            imageflip($source, $flip);
        }

        return $source;
    }

    private function deleteQuietly(string $disk, string $path): void
    {
        try {
            Storage::disk($disk)->delete($path);
        } catch (Throwable $e) {
            Log::warning('Post image thumbnail cleanup failed.', [
                'disk' => $disk,
                'path' => $path,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);
        }
    }

    private function normalizePublicUrlPath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return '/'.ltrim($url, '/');
        }

        return '/'.ltrim($path, '/');
    }

    /**
     * @return array<string, string>
     */
    private function storageWriteOptions(string $disk): array
    {
        return $disk === 's3' ? [] : ['visibility' => 'public'];
    }

    private function width(): int
    {
        return (int) config('posts.thumbnail.width', 800);
    }

    private function height(): int
    {
        return (int) config('posts.thumbnail.height', 550);
    }

    private function quality(): int
    {
        return (int) config('posts.thumbnail.quality', 82);
    }

    private function bodyMaxWidth(): int
    {
        return max(1, (int) config('posts.body_image.max_width', 800));
    }

    private function bodyQuality(): int
    {
        return (int) config('posts.body_image.quality', 75);
    }
}
