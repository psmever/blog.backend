<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostImageVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class OptimizePostBodyImagesCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://api.test.local',
            'filesystems.media_disk' => 'public',
            'posts.image_base_url' => 'https://images.test.local',
            'posts.body_image.max_width' => 800,
            'posts.body_image.quality' => 75,
        ]);

        $mediaRoot = trim((string) config('filesystems.media_root', 'blog'), '/');
        config([
            'filesystems.disks.public.url' => rtrim((string) config('app.url'), '/').'/storage'.($mediaRoot !== '' ? '/'.$mediaRoot : ''),
        ]);

        Storage::fake('public', [
            'url' => (string) config('filesystems.disks.public.url'),
        ]);
    }

    public function test_optimize_body_images_regenerates_variant_and_rewrites_post_body(): void
    {
        [$post, $image] = $this->createPostWithImage();

        $image->variants()->create([
            'variant' => PostImageVariant::VARIANT_BODY,
            'disk' => 'public',
            'path' => 'posts/'.$image->post_uuid.'/body-resized/'.$image->uuid.'.webp',
            'url' => '/storage/blog/posts/'.$image->post_uuid.'/body-resized/'.$image->uuid.'.webp',
            'mime_type' => 'image/webp',
            'size' => 1,
            'width' => 1200,
            'height' => 800,
        ]);

        $this->artisan('posts:optimize-body-images --chunk=1')
            ->expectsOutput('Post body image optimization completed: processed=1, optimized=1, rewritten_posts=1, rewritten_urls=1, failed=0')
            ->assertExitCode(0);

        $bodyImage = $image->bodyVariant()->firstOrFail();
        $this->assertSame(PostImageVariant::VARIANT_BODY, $bodyImage->variant);
        $this->assertSame('posts/'.$image->post_uuid.'/body-resized/'.$image->uuid.'.webp', $bodyImage->path);
        $this->assertSame(800, $bodyImage->width);
        $this->assertSame(533, $bodyImage->height);
        $this->assertGreaterThan(1, $bodyImage->size);
        Storage::disk('public')->assertExists($bodyImage->path);

        $post->refresh();
        $this->assertStringContainsString(
            'https://images.test.local/storage/blog/posts/'.$image->post_uuid.'/body-resized/'.$image->uuid.'.webp',
            (string) $post->body
        );
        $this->assertStringNotContainsString(
            'https://images.test.local/storage/blog/posts/'.$image->post_uuid.'/body/'.$image->uuid.'.png',
            (string) $post->body
        );
    }

    public function test_optimize_body_images_dry_run_does_not_write_files_or_update_posts(): void
    {
        [$post, $image] = $this->createPostWithImage();
        $originalBody = (string) $post->body;

        $this->artisan('posts:optimize-body-images --dry-run --chunk=1')
            ->expectsOutput('Post body image optimization dry-run completed: processed=1, optimized=1, rewritten_posts=1, rewritten_urls=1, failed=0')
            ->assertExitCode(0);

        $this->assertNull($image->bodyVariant()->first());
        Storage::disk('public')->assertMissing('posts/'.$image->post_uuid.'/body-resized/'.$image->uuid.'.webp');
        $this->assertSame($originalBody, (string) $post->refresh()->body);
    }

    /**
     * @return array{0: Post, 1: PostImage}
     */
    private function createPostWithImage(): array
    {
        $user = User::factory()->create();
        $postUuid = (string) Str::uuid();
        $imageUuid = (string) Str::uuid();
        $path = 'posts/'.$postUuid.'/body/'.$imageUuid.'.png';
        $originalUrl = 'https://images.test.local/storage/blog/'.$path;

        Storage::disk('public')->put($path, $this->pngBytes(1200, 800));

        $post = Post::query()->create([
            'uuid' => $postUuid,
            'user_id' => $user->getKey(),
            'title' => 'Body Image Post',
            'slug' => 'body-image-post',
            'status' => Post::STATUS_DRAFT,
            'published_at' => null,
            'body' => '본문입니다.'."\n\n".'![body]('.$originalUrl.')',
        ]);

        $image = PostImage::query()->create([
            'uuid' => $imageUuid,
            'post_id' => $post->getKey(),
            'user_id' => $user->getKey(),
            'post_uuid' => $postUuid,
            'purpose' => PostImage::PURPOSE_BODY,
            'disk' => 'public',
            'path' => $path,
            'url' => '/storage/blog/'.$path,
            'original_name' => 'body.png',
            'mime_type' => 'image/png',
            'size' => Storage::disk('public')->size($path),
            'width' => 1200,
            'height' => 800,
        ]);

        return [$post, $image];
    }

    private function pngBytes(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        imagepng($image);
        $contents = ob_get_clean() ?: '';
        imagedestroy($image);

        return $contents;
    }
}
