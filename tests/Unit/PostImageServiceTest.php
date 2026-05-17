<?php

namespace Tests\Unit;

use App\Models\PostImage;
use App\Repositories\PostImageRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Services\PostImageService;
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
}
