<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\PostImage;
use App\Models\PostImageVariant;
use App\Models\User;
use App\Repositories\PostImageRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PostImageService
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly PostImageRepositoryInterface $postImages,
        private readonly PostImageThumbnailService $thumbnails
    ) {}

    public function uploadForPost(User $user, string $postUuid, UploadedFile $image): ?PostImage
    {
        $this->ensureUploadAvailable();

        $imageUuid = (string) Str::uuid();
        $extension = $image->extension() ?: $image->guessExtension() ?: 'bin';
        $disk = $this->mediaDisk();
        $path = sprintf('posts/%s/%s/%s.%s', $postUuid, PostImage::PURPOSE_BODY, $imageUuid, $extension);
        $thumbnailPath = sprintf('posts/%s/%s/%s.webp', $postUuid, PostImageVariant::VARIANT_THUMBNAIL, $imageUuid);

        try {
            return DB::transaction(function () use ($user, $postUuid, $image, $imageUuid, $disk, $path) {
                $post = $this->posts->findByUuidForUser($user->getKey(), $postUuid);
                if (! $post && $this->posts->uuidExists($postUuid)) {
                    return null;
                }

                $storedPath = Storage::disk($disk)->putFileAs(
                    dirname($path),
                    $image,
                    basename($path),
                    $this->storageWriteOptions($disk)
                );
                $fileExists = $storedPath !== false && Storage::disk($disk)->exists($path);

                if ($storedPath === false || ! $fileExists) {
                    throw new RuntimeException('Image storage write returned without a persisted file.');
                }

                [$width, $height] = $this->dimensions($image);

                $postImage = $this->postImages->create([
                    'uuid' => $imageUuid,
                    'post_id' => $post ? (int) $post->getKey() : null,
                    'user_id' => (int) $user->getKey(),
                    'post_uuid' => $postUuid,
                    'purpose' => PostImage::PURPOSE_BODY,
                    'disk' => $disk,
                    'path' => $path,
                    'url' => $this->publicUrlPath($disk, $path),
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType() ?: 'application/octet-stream',
                    'size' => $image->getSize() ?: 0,
                    'width' => $width,
                    'height' => $height,
                ]);

                $sourceBytes = file_get_contents($image->getRealPath());
                if (! is_string($sourceBytes)) {
                    throw new RuntimeException('Failed to read uploaded image.');
                }

                $this->thumbnails->createForImage($postImage, $sourceBytes, $image->getRealPath());

                return $postImage;
            });
        } catch (Throwable $e) {
            $this->deleteQuietly($disk, [$path, $thumbnailPath]);

            if ($e instanceof ApiException) {
                throw $e;
            }

            Log::error('Post image storage write failed.', [
                'disk' => $disk,
                'path' => $path,
                'post_uuid' => $postUuid,
                'user_id' => (int) $user->getKey(),
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);

            throw new ApiException(
                sprintf('이미지 파일 저장에 실패했습니다. [%s] 디스크 설정과 접근 권한을 확인해 주세요.', $disk),
                500
            );
        }
    }

    public function urlForImage(PostImage $image): string
    {
        return $this->responseUrl($this->publicUrlPath($image->disk, $image->path));
    }

    public function urlForVariant(PostImageVariant $variant): string
    {
        return $this->responseUrl($this->publicUrlPath($variant->disk, $variant->path));
    }

    public function responseUrl(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return $url;
        }

        $normalizedUrl = $this->normalizePublicUrlPath($url);
        $baseUrl = $this->imageBaseUrl();

        if ($baseUrl === '') {
            return $normalizedUrl;
        }

        return $baseUrl.$normalizedUrl;
    }

    public function isUploadAvailable(): bool
    {
        return $this->imageBaseUrl() !== '';
    }

    private function mediaDisk(): string
    {
        $disk = config('filesystems.media_disk', 'public');

        return is_string($disk) && $disk !== '' ? $disk : 'public';
    }

    private function ensureUploadAvailable(): void
    {
        if ($this->isUploadAvailable()) {
            return;
        }

        throw new RuntimeException('이미지 업로드를 사용할 수 없습니다. APP_IMAGE_URL 설정이 필요합니다.');
    }

    private function imageBaseUrl(): string
    {
        return rtrim(trim((string) config('posts.image_base_url', '')), '/');
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteQuietly(string $disk, array $paths): void
    {
        try {
            Storage::disk($disk)->delete($paths);
        } catch (Throwable $e) {
            Log::warning('Post image cleanup failed.', [
                'disk' => $disk,
                'paths' => $paths,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    private function storageWriteOptions(string $disk): array
    {
        if ($disk === 's3') {
            return [];
        }

        return ['visibility' => 'public'];
    }

    private function publicUrlPath(string $disk, string $path): string
    {
        if ($legacyUrl = $this->legacyLocalPublicUrlPath($disk, $path)) {
            return $legacyUrl;
        }

        $storageUrl = Storage::disk($disk)->url($path);

        return $this->normalizePublicUrlPath($storageUrl);
    }

    private function legacyLocalPublicUrlPath(string $disk, string $path): ?string
    {
        if ($disk !== 'public') {
            return null;
        }

        $mediaRoot = trim((string) config('filesystems.media_root', ''), '/');
        if ($mediaRoot === '') {
            return null;
        }

        if (Storage::disk($disk)->exists($path)) {
            return null;
        }

        $legacyPath = storage_path('app/public/'.ltrim($path, '/'));
        if (! is_file($legacyPath)) {
            return null;
        }

        return '/storage/'.ltrim($path, '/');
    }

    private function normalizePublicUrlPath(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '/';
        }

        $urlPath = parse_url($url, PHP_URL_PATH);
        $urlQuery = parse_url($url, PHP_URL_QUERY);
        $urlFragment = parse_url($url, PHP_URL_FRAGMENT);

        if (! is_string($urlPath) || $urlPath === '') {
            $urlPath = '/'.ltrim($url, '/');
        } elseif (! str_starts_with($urlPath, '/')) {
            $urlPath = '/'.$urlPath;
        }

        return $urlPath
            .(is_string($urlQuery) && $urlQuery !== '' ? '?'.$urlQuery : '')
            .(is_string($urlFragment) && $urlFragment !== '' ? '#'.$urlFragment : '');
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function dimensions(UploadedFile $image): array
    {
        $dimensions = @getimagesize($image->getRealPath());
        if (! is_array($dimensions)) {
            return [null, null];
        }

        return [(int) $dimensions[0], (int) $dimensions[1]];
    }
}
