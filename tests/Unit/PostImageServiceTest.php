<?php

namespace Tests\Unit;

use App\Exceptions\ApiException;
use App\Models\PostImage;
use App\Models\User;
use App\Repositories\PostImageRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Services\PostImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class PostImageServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_prefixes_relative_local_storage_urls_with_app_url(): void
    {
        config(['posts.image_base_url' => 'https://images.jaubi.co.kr']);

        Storage::shouldReceive('disk->url')
            ->once()
            ->with('posts/sample/body.png')
            ->andReturn('/storage/posts/sample/body.png');

        $service = new PostImageService(
            Mockery::mock(PostRepositoryInterface::class),
            Mockery::mock(PostImageRepositoryInterface::class),
        );

        $image = new PostImage([
            'disk' => 'public',
            'path' => 'posts/sample/body.png',
        ]);

        $this->assertSame(
            'https://images.jaubi.co.kr/storage/posts/sample/body.png',
            $service->urlForImage($image),
        );
    }

    public function test_it_prefixes_domainless_cloud_storage_urls_with_image_base_url(): void
    {
        config(['posts.image_base_url' => 'https://images.jaubi.co.kr']);

        Storage::shouldReceive('disk->url')
            ->once()
            ->with('posts/sample/body.png')
            ->andReturn('https://cdn.jaubi.co.kr/posts/sample/body.png');

        $service = new PostImageService(
            Mockery::mock(PostRepositoryInterface::class),
            Mockery::mock(PostImageRepositoryInterface::class),
        );

        $image = new PostImage([
            'disk' => 's3',
            'path' => 'posts/sample/body.png',
        ]);

        $this->assertSame(
            'https://images.jaubi.co.kr/posts/sample/body.png',
            $service->urlForImage($image),
        );
    }

    public function test_it_throws_when_storage_write_fails(): void
    {
        config(['posts.image_base_url' => 'https://images.jaubi.co.kr']);
        config(['filesystems.media_disk' => 's3']);

        $posts = Mockery::mock(PostRepositoryInterface::class);
        $posts->shouldReceive('findByUuidForUser')
            ->once()
            ->with(1, 'post-uuid')
            ->andReturn(null);
        $posts->shouldReceive('uuidExists')
            ->once()
            ->with('post-uuid')
            ->andReturn(false);

        $postImages = Mockery::mock(PostImageRepositoryInterface::class);
        $postImages->shouldNotReceive('create');

        Log::shouldReceive('error')
            ->once()
            ->with('Post image storage write failed.', Mockery::type('array'));

        Storage::shouldReceive('disk->putFileAs')
            ->once()
            ->with(
                Mockery::type('string'),
                Mockery::type(UploadedFile::class),
                Mockery::type('string'),
                []
            )
            ->andThrow(new RuntimeException('S3 write failed'));
        Storage::shouldReceive('disk->exists')
            ->never();

        $service = new PostImageService($posts, $postImages);

        $user = new User;
        $user->id = 1;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('이미지 파일 저장에 실패했습니다. [s3] 디스크 설정과 접근 권한을 확인해 주세요.');

        $service->uploadForPost(
            $user,
            'post-uuid',
            UploadedFile::fake()->image('body.png')
        );
    }

    public function test_it_uses_public_visibility_for_local_disk_writes(): void
    {
        config(['posts.image_base_url' => 'https://images.jaubi.co.kr']);
        config(['filesystems.media_disk' => 'public']);

        $posts = Mockery::mock(PostRepositoryInterface::class);
        $posts->shouldReceive('findByUuidForUser')
            ->once()
            ->with(1, 'post-uuid')
            ->andReturn(null);
        $posts->shouldReceive('uuidExists')
            ->once()
            ->with('post-uuid')
            ->andReturn(false);

        $createdImage = new PostImage([
            'uuid' => 'image-uuid',
            'path' => 'posts/post-uuid/body/generated-image.png',
            'disk' => 'public',
        ]);

        $postImages = Mockery::mock(PostImageRepositoryInterface::class);
        $postImages->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $attributes): bool {
                return $attributes['disk'] === 'public'
                    && str_starts_with($attributes['path'], 'posts/post-uuid/body/')
                    && str_ends_with($attributes['path'], '.png')
                    && str_starts_with($attributes['url'], '/storage/posts/post-uuid/body/')
                    && str_ends_with($attributes['url'], '.png');
            }))
            ->andReturn($createdImage);

        Storage::shouldReceive('disk->putFileAs')
            ->once()
            ->with(
                Mockery::type('string'),
                Mockery::type(UploadedFile::class),
                Mockery::type('string'),
                ['visibility' => 'public']
            )
            ->andReturn('posts/post-uuid/body/image-uuid.png');
        Storage::shouldReceive('disk->exists')
            ->once()
            ->andReturn(true);
        Storage::shouldReceive('disk->url')
            ->once()
            ->andReturn('/storage/posts/post-uuid/body/image-uuid.png');

        $service = new PostImageService($posts, $postImages);

        $user = new User;
        $user->id = 1;

        $image = $service->uploadForPost(
            $user,
            'post-uuid',
            UploadedFile::fake()->image('body.png')
        );

        $this->assertSame($createdImage, $image);
    }
}
