<?php

namespace Tests\Feature;

use App\Models\PostImage;
use App\Models\PostImageVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BackfillPostImageThumbnailsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
    }

    public function test_backfill_command_generates_missing_thumbnails_and_is_idempotent(): void
    {
        $user = User::factory()->create();
        $postUuid = (string) Str::uuid();
        $imageUuid = (string) Str::uuid();
        $path = 'posts/'.$postUuid.'/body/'.$imageUuid.'.png';

        Storage::disk('public')->put($path, $this->pngBytes());

        $image = PostImage::query()->create([
            'uuid' => $imageUuid,
            'post_id' => null,
            'user_id' => $user->getKey(),
            'post_uuid' => $postUuid,
            'purpose' => PostImage::PURPOSE_BODY,
            'disk' => 'public',
            'path' => $path,
            'url' => '/storage/blog/'.$path,
            'original_name' => 'body.png',
            'mime_type' => 'image/png',
            'size' => Storage::disk('public')->size($path),
            'width' => 10,
            'height' => 10,
        ]);

        $this->artisan('posts:backfill-thumbnails --chunk=1')
            ->expectsOutput('Post image thumbnail backfill completed: created=1, skipped=0, failed=0')
            ->assertExitCode(0);

        $this->assertDatabaseCount('post_image_variants', 1);
        $thumbnail = $image->thumbnailVariant()->firstOrFail();
        $this->assertSame(PostImageVariant::VARIANT_THUMBNAIL, $thumbnail->variant);
        $this->assertSame('posts/'.$postUuid.'/thumbnail/'.$imageUuid.'.webp', $thumbnail->path);
        Storage::disk('public')->assertExists($thumbnail->path);

        $this->artisan('posts:backfill-thumbnails --chunk=1')
            ->expectsOutput('Post image thumbnail backfill completed: created=0, skipped=1, failed=0')
            ->assertExitCode(0);

        $this->assertDatabaseCount('post_image_variants', 1);
    }

    private function pngBytes(): string
    {
        $image = imagecreatetruecolor(10, 10);
        ob_start();
        imagepng($image);
        $contents = ob_get_clean() ?: '';
        imagedestroy($image);

        return $contents;
    }
}
