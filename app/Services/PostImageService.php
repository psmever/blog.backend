<?php

namespace App\Services;

use App\Models\PostImage;
use App\Models\User;
use App\Repositories\PostImageRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PostImageService
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly PostImageRepositoryInterface $postImages
    ) {}

    public function uploadForPost(User $user, string $postUuid, UploadedFile $image): ?PostImage
    {
        $this->ensureUploadAvailable();

        return DB::transaction(function () use ($user, $postUuid, $image) {
            $post = $this->posts->findByUuidForUser($user->getKey(), $postUuid);
            if (! $post && $this->posts->uuidExists($postUuid)) {
                return null;
            }

            $imageUuid = (string) Str::uuid();
            $extension = $image->extension() ?: $image->guessExtension() ?: 'bin';
            $disk = $this->mediaDisk();
            $path = sprintf('posts/%s/%s/%s.%s', $postUuid, PostImage::PURPOSE_BODY, $imageUuid, $extension);

            Storage::disk($disk)->putFileAs(
                dirname($path),
                $image,
                basename($path),
                ['visibility' => 'public']
            );

            [$width, $height] = $this->dimensions($image);

            return $this->postImages->create([
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
        });
    }

    public function urlForImage(PostImage $image): string
    {
        return $this->responseUrl($this->publicUrlPath($image->disk, $image->path));
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

    private function publicUrlPath(string $disk, string $path): string
    {
        $storageUrl = Storage::disk($disk)->url($path);

        return $this->normalizePublicUrlPath($storageUrl);
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
