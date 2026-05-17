<?php

namespace Tests\Feature;

use App\Models\PostImage;
use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
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
        Storage::fake('public');
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
            ->assertJsonPath('data.width', 1)
            ->assertJsonPath('data.height', 1);

        /** @var PostImage $image */
        $image = PostImage::query()->where('uuid', $response->json('data.uuid'))->firstOrFail();
        $expectedUrl = rtrim((string) config('posts.image_base_url'), '/').'/storage/'.$image->path;
        $expectedStoredUrl = '/storage/'.$image->path;

        $response->assertJsonPath('data.url', $expectedUrl);
        $this->assertSame($expectedStoredUrl, $image->url);
        Storage::disk('public')->assertExists($image->path);
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
        config(['posts.image_upload_max_kb' => 1]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postUuid = $this->createPost();

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/images', [
            'purpose' => PostImage::PURPOSE_BODY,
            'image' => $this->fakePng('large.png', 2048),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        $this->assertDatabaseCount('post_images', 0);
    }

    private function fakePng(string $name, int $minBytes = 0): UploadedFile
    {
        $contents = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=') ?: '';

        if ($minBytes > strlen($contents)) {
            $contents .= str_repeat('0', $minBytes - strlen($contents));
        }

        return UploadedFile::fake()->createWithContent(
            $name,
            $contents
        );
    }
}
