<?php

namespace Tests\Feature;

use App\Exceptions\ApiException;
use App\Models\PostImage;
use App\Models\PostImageVariant;
use App\Models\User;
use App\Services\PostImageThumbnailService;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class PostImageTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
        config(['app.url' => 'https://api.test.local']);
        config(['posts.image_base_url' => 'https://images.test.local']);
        config(['filesystems.media_disk' => 'public']);
        $mediaRoot = trim((string) config('filesystems.media_root', 'blog'), '/');
        config([
            'filesystems.disks.public.url' => rtrim((string) config('app.url'), '/').'/storage'.($mediaRoot !== '' ? '/'.$mediaRoot : ''),
        ]);
        Storage::fake('public', [
            'url' => (string) config('filesystems.disks.public.url'),
        ]);
    }

    private function postWithClientType(string $uri, array $payload)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->postJson($uri, $payload);
    }

    private function createPost(): string
    {
        $response = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Image Post',
            'tags' => ['laravel'],
            'body' => 'body',
        ])->assertCreated();

        return (string) $response->json('data.uuid');
    }

    public function test_upload_body_image_for_post(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $response = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('body.png'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.purpose', PostImage::PURPOSE_BODY)
            ->assertJsonPath('data.width', 10)
            ->assertJsonPath('data.height', 10)
            ->assertJsonPath('data.body_image.width', 10)
            ->assertJsonPath('data.body_image.height', 10)
            ->assertJsonPath('data.body_image.mime_type', 'image/webp')
            ->assertJsonPath('data.thumbnail.width', 800)
            ->assertJsonPath('data.thumbnail.height', 550)
            ->assertJsonPath('data.thumbnail.mime_type', 'image/webp');

        /** @var PostImage $image */
        $image = PostImage::query()->where('uuid', $response->json('data.uuid'))->firstOrFail();
        $mediaRoot = trim((string) config('filesystems.media_root', ''), '/');
        $prefixedPath = ltrim(($mediaRoot !== '' ? $mediaRoot.'/' : '').$image->path, '/');
        $expectedUrl = rtrim((string) config('posts.image_base_url'), '/').'/storage/'.$prefixedPath;
        $expectedStoredUrl = '/storage/'.$prefixedPath;

        $response->assertJsonPath('data.url', $expectedUrl);
        $this->assertSame($expectedStoredUrl, $image->url);
        Storage::disk('public')->assertExists($image->path);
        $thumbnail = $image->thumbnailVariant()->firstOrFail();
        $this->assertSame('posts/'.$postUuid.'/thumbnail/'.$image->uuid.'.webp', $thumbnail->path);
        Storage::disk('public')->assertExists($thumbnail->path);

        $bodyImage = $image->bodyVariant()->firstOrFail();
        $this->assertSame(PostImageVariant::VARIANT_BODY, $bodyImage->variant);
        $this->assertSame('posts/'.$postUuid.'/body-resized/'.$image->uuid.'.webp', $bodyImage->path);
        Storage::disk('public')->assertExists($bodyImage->path);
    }

    public function test_issue_post_uuid_without_creating_post(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postWithClientType('/api/v1/posts/uuid', [])
            ->assertOk()
            ->assertJsonStructure(['data' => ['uuid']]);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_upload_image_before_post_exists_and_attach_on_save(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $issued = $this->postWithClientType('/api/v1/posts/uuid', [])->assertOk();
        $postUuid = (string) $issued->json('data.uuid');

        $upload = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('body.png'),
        ])->assertCreated();

        $imageUuid = (string) $upload->json('data.uuid');

        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseHas('post_images', [
            'uuid' => $imageUuid,
            'post_uuid' => $postUuid,
            'post_id' => null,
        ]);

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/save', [
            'title' => 'Saved Later',
            'tags' => ['laravel'],
            'body' => 'body',
        ])->assertOk()
            ->assertJsonPath('data.uuid', $postUuid);

        $this->assertDatabaseHas('posts', [
            'uuid' => $postUuid,
            'title' => 'Saved Later',
        ]);

        $image = PostImage::query()->where('uuid', $imageUuid)->firstOrFail();
        $this->assertNotNull($image->post_id);
    }

    public function test_save_uses_first_body_image_as_cover_image(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $issued = $this->postWithClientType('/api/v1/posts/uuid', [])->assertOk();
        $postUuid = (string) $issued->json('data.uuid');

        $firstUpload = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('first.png'),
        ])->assertCreated();

        $secondUpload = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('second.png'),
        ])->assertCreated();

        $firstUrl = (string) $firstUpload->json('data.url');
        $secondUrl = (string) $secondUpload->json('data.url');
        $firstImageUuid = (string) $firstUpload->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/save', [
            'title' => 'Saved Later',
            'tags' => ['laravel'],
            'body' => sprintf("![first](%s)\n\n![second](%s)", $firstUrl, $secondUrl),
        ])->assertOk();

        $this->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/posts/'.$postUuid)
            ->assertOk()
            ->assertJsonPath('data.cover_image.uuid', $firstImageUuid)
            ->assertJsonPath('data.cover_image.purpose', PostImage::PURPOSE_BODY);
    }

    public function test_save_uses_body_resized_image_url_as_cover_image(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $issued = $this->postWithClientType('/api/v1/posts/uuid', [])->assertOk();
        $postUuid = (string) $issued->json('data.uuid');

        $upload = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('body.png'),
        ])->assertCreated();

        $bodyImageUrl = (string) $upload->json('data.body_image.url');
        $imageUuid = (string) $upload->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/save', [
            'title' => 'Saved With Resized Body Image',
            'tags' => ['laravel'],
            'body' => sprintf('![body](%s)', $bodyImageUrl),
        ])->assertOk();

        $this->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/posts/'.$postUuid)
            ->assertOk()
            ->assertJsonPath('data.cover_image.uuid', $imageUuid)
            ->assertJsonPath('data.cover_image.purpose', PostImage::PURPOSE_BODY)
            ->assertJsonPath('data.cover_image.is_default', false);
    }

    public function test_save_updates_cover_image_when_first_body_image_changes(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $issued = $this->postWithClientType('/api/v1/posts/uuid', [])->assertOk();
        $postUuid = (string) $issued->json('data.uuid');

        $firstUpload = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('first.png'),
        ])->assertCreated();

        $secondUpload = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('second.png'),
        ])->assertCreated();

        $firstUrl = (string) $firstUpload->json('data.url');
        $secondUrl = (string) $secondUpload->json('data.url');
        $firstImageUuid = (string) $firstUpload->json('data.uuid');
        $secondImageUuid = (string) $secondUpload->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/save', [
            'title' => 'Saved Later',
            'tags' => ['laravel'],
            'body' => sprintf("![first](%s)\n\n![second](%s)", $firstUrl, $secondUrl),
        ])->assertOk();

        $this->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/posts/'.$postUuid)
            ->assertOk()
            ->assertJsonPath('data.cover_image.uuid', $firstImageUuid);

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/save', [
            'body' => sprintf("![second](%s)\n\n![first](%s)", $secondUrl, $firstUrl),
        ])->assertOk();

        $this->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/posts/'.$postUuid)
            ->assertOk()
            ->assertJsonPath('data.cover_image.uuid', $secondImageUuid)
            ->assertJsonPath('data.cover_image.purpose', PostImage::PURPOSE_BODY);
    }

    public function test_upload_image_returns_not_found_for_other_user_post(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $postUuid = $this->createPost();

        $other = User::factory()->create();
        Sanctum::actingAs($other);

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('body.png'),
        ])->assertNotFound();
    }

    public function test_upload_image_ignores_non_body_purpose_payload(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $response = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => 'cover',
            'image' => $this->fakePng('body.png'),
        ])->assertCreated();

        $response->assertJsonPath('data.purpose', PostImage::PURPOSE_BODY);
    }

    public function test_upload_image_validates_file_type(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ])->assertUnprocessable();
    }

    public function test_upload_image_is_unavailable_when_image_base_url_is_missing(): void
    {
        config(['posts.image_base_url' => '']);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('body.png'),
        ])->assertStatus(503)
            ->assertJsonPath('message', '이미지 업로드를 사용할 수 없습니다. APP_IMAGE_URL 설정이 필요합니다.');

        $this->assertDatabaseCount('post_images', 0);
    }

    public function test_upload_image_validates_file_size(): void
    {
        config([
            'app.locale' => 'ko',
            'app.fallback_locale' => 'ko',
            'posts.image_upload_max_kb' => 1,
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('large.png', 2048),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image')
            ->assertJsonPath('errors.image.0', '이미지 항목은 1 KB보다 클 수 없습니다.');

        $this->assertDatabaseCount('post_images', 0);
    }

    public function test_upload_generates_webp_thumbnail_for_supported_image_formats(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        foreach (['jpg', 'png', 'webp', 'gif'] as $extension) {
            $postUuid = $this->createPost();

            $response = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
                'image' => $this->fakeImage('body.'.$extension, $extension),
            ])->assertCreated();

            $response
                ->assertJsonPath('data.body_image.mime_type', 'image/webp')
                ->assertJsonPath('data.thumbnail.width', 800)
                ->assertJsonPath('data.thumbnail.height', 550)
                ->assertJsonPath('data.thumbnail.mime_type', 'image/webp');

            $image = PostImage::query()->where('uuid', $response->json('data.uuid'))->firstOrFail();
            $bodyImage = $image->bodyVariant()->firstOrFail();
            $thumbnail = $image->thumbnailVariant()->firstOrFail();

            $this->assertSame('posts/'.$postUuid.'/body-resized/'.$image->uuid.'.webp', $bodyImage->path);
            Storage::disk('public')->assertExists($bodyImage->path);
            $this->assertSame('posts/'.$postUuid.'/thumbnail/'.$image->uuid.'.webp', $thumbnail->path);
            Storage::disk('public')->assertExists($thumbnail->path);
        }
    }

    public function test_upload_resizes_body_image_to_configured_max_width_without_changing_ratio(): void
    {
        config(['posts.body_image.max_width' => 6]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $response = $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'image' => $this->fakePng('wide.png', 0, 12, 8),
        ])->assertCreated();

        $response
            ->assertJsonPath('data.width', 12)
            ->assertJsonPath('data.height', 8)
            ->assertJsonPath('data.body_image.width', 6)
            ->assertJsonPath('data.body_image.height', 4)
            ->assertJsonPath('data.body_image.mime_type', 'image/webp');

        $image = PostImage::query()->where('uuid', $response->json('data.uuid'))->firstOrFail();
        $bodyImage = $image->bodyVariant()->firstOrFail();

        $this->assertSame(PostImageVariant::VARIANT_BODY, $bodyImage->variant);
        $this->assertSame(6, $bodyImage->width);
        $this->assertSame(4, $bodyImage->height);
        Storage::disk('public')->assertExists($bodyImage->path);
    }

    public function test_upload_removes_original_file_and_database_record_when_thumbnail_generation_fails(): void
    {
        $thumbnails = Mockery::mock(PostImageThumbnailService::class);
        $thumbnails->shouldReceive('createForImage')
            ->once()
            ->andThrow(new ApiException('이미지 썸네일 생성에 실패했습니다.', 500));
        $thumbnails->shouldReceive('createBodyForImage')
            ->never();
        $this->app->instance(PostImageThumbnailService::class, $thumbnails);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'image' => $this->fakePng('body.png'),
        ])->assertStatus(500)
            ->assertJsonPath('message', '이미지 썸네일 생성에 실패했습니다.');

        $this->assertDatabaseCount('post_images', 0);
        $this->assertDatabaseCount('post_image_variants', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    private function fakePng(string $name, int $minBytes = 0, int $width = 10, int $height = 10): UploadedFile
    {
        $contents = $this->imageBytes('png', $width, $height);

        if ($minBytes > strlen($contents)) {
            $contents .= str_repeat('0', $minBytes - strlen($contents));
        }

        return UploadedFile::fake()->createWithContent(
            $name,
            $contents
        );
    }

    private function fakeImage(string $name, string $extension): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $this->imageBytes($extension));
    }

    private function imageBytes(string $extension, int $width = 10, int $height = 10): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        match ($extension) {
            'jpg' => imagejpeg($image),
            'png' => imagepng($image),
            'webp' => imagewebp($image),
            'gif' => imagegif($image),
        };
        $contents = ob_get_clean() ?: '';
        imagedestroy($image);

        return $contents;
    }
}
