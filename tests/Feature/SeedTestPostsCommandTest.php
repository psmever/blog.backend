<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeedTestPostsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://api.test.local']);
        config(['posts.image_base_url' => 'https://images.test.local']);
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
    }

    public function test_seed_test_posts_creates_default_korean_published_posts_with_images(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $this->artisan('posts:seed-test')
            ->assertExitCode(0);

        $this->assertDatabaseCount('posts', 50);
        $this->assertDatabaseCount('post_images', 50);

        $posts = Post::query()
            ->with('coverImage')
            ->orderBy('id')
            ->get();

        $this->assertCount(50, $posts->pluck('title')->unique());

        foreach ($posts as $post) {
            $this->assertSame(Post::STATUS_PUBLISHED, $post->status);
            $this->assertNotNull($post->published_at);
            $this->assertNotNull($post->cover_image_id);
            $this->assertNotNull($post->coverImage);
            if ($post->coverImage->mime_type === 'image/png') {
                $this->assertSame(1200, $post->coverImage->width);
                $this->assertSame(630, $post->coverImage->height);
            } else {
                $this->assertSame('image/svg+xml', $post->coverImage->mime_type);
            }
            $this->assertMatchesRegularExpression('/[가-힣]/u', $post->title);
            $this->assertStringContainsString('![테스트 커버 이미지', $post->body);
            $this->assertGreaterThan(500, mb_strlen($post->body, 'UTF-8'));
            Storage::disk('public')->assertExists($post->coverImage->path);
        }
    }

    public function test_seed_test_posts_respects_count_and_no_images_options(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $this->artisan('posts:seed-test --count=3 --no-images')
            ->assertExitCode(0);

        $this->assertDatabaseCount('posts', 3);
        $this->assertDatabaseCount('post_images', 0);

        Post::query()->each(function (Post $post): void {
            $this->assertSame(Post::STATUS_PUBLISHED, $post->status);
            $this->assertNull($post->cover_image_id);
            $this->assertStringNotContainsString('![테스트 커버 이미지', $post->body);
            $this->assertGreaterThan(500, mb_strlen($post->body, 'UTF-8'));
        });
    }

    public function test_seed_test_posts_can_create_drafts(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $this->artisan('posts:seed-test --count=2 --status=draft')
            ->assertExitCode(0);

        $this->assertDatabaseCount('posts', 2);

        Post::query()->each(function (Post $post): void {
            $this->assertSame(Post::STATUS_DRAFT, $post->status);
            $this->assertNull($post->published_at);
        });
    }

    public function test_seed_test_posts_fails_outside_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'testing');

        $this->artisan('posts:seed-test')
            ->expectsOutput('This command can only be run in the local environment.')
            ->assertExitCode(1);

        $this->assertDatabaseCount('posts', 0);
    }
}
