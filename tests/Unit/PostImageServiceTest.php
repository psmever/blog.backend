<?php

namespace Tests\Unit;

use App\Exceptions\ApiException;
use App\Models\PostImage;
use App\Models\User;
use App\Repositories\PostImageRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Services\PostImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
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

        Storage::shouldReceive('disk->putFileAs')
            ->once()
            ->andReturn(false);
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
}
