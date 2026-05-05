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

class PostImageService
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly PostImageRepositoryInterface $postImages
    ) {}

    public function uploadForPost(User $user, string $postUuid, UploadedFile $image): ?PostImage
    {
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
                'url' => $this->publicUrl($disk, $path),
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
        return $this->publicUrl($image->disk, $image->path);
    }

    private function mediaDisk(): string
    {
        $disk = config('filesystems.media_disk', 'public');

        return is_string($disk) && $disk !== '' ? $disk : 'public';
    }

    private function publicUrl(string $disk, string $path): string
    {
        $storageUrl = Storage::disk($disk)->url($path);
        $appUrl = rtrim((string) config('app.url', ''), '/');

        if ($appUrl === '') {
            return $storageUrl;
        }

        $urlPath = parse_url($storageUrl, PHP_URL_PATH);
        $urlQuery = parse_url($storageUrl, PHP_URL_QUERY);
        $urlFragment = parse_url($storageUrl, PHP_URL_FRAGMENT);

        if (! is_string($urlPath) || $urlPath === '') {
            $urlPath = '/'.ltrim($storageUrl, '/');
        } elseif (! str_starts_with($urlPath, '/')) {
            $urlPath = '/'.$urlPath;
        }

        return $appUrl
            .$urlPath
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
