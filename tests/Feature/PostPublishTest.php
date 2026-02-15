<?php

namespace Tests\Feature;

use App\Models\CommonCode;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostPublishTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
    }

    private function postWithClientType(string $uri, array $payload)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->postJson($uri, $payload);
    }

    public function test_publish_post_by_uuid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Publish Me',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])
            ->assertOk()
            ->assertJsonPath('data.uuid', $uuid);

        /** @var Post $post */
        $post = Post::query()->where('uuid', $uuid)->firstOrFail();
        $this->assertSame(Post::STATUS_PUBLISHED, $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_publish_requires_title_tags_body(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => '',
            'body' => '',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])
            ->assertStatus(422)
            ->assertJsonPath('errors.title.0', '제목은 필수입니다.')
            ->assertJsonPath('errors.body.0', '본문은 필수입니다.')
            ->assertJsonPath('errors.tags.0', '태그는 최소 1개 이상 필요합니다.');
    }

    public function test_publish_fails_when_status_common_code_is_inactive(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Publish Me',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        CommonCode::query()
            ->forGroup('post.status')
            ->where('code', 'published')
            ->update(['is_active' => false]);

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])
            ->assertStatus(500)
            ->assertJsonPath('message', '공통코드 설정 오류입니다. [post.status:published]');
    }
}
