<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageMetaTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
        config([
            'app.name' => '테스트 블로그',
            'app.display_timezone' => 'Asia/Seoul',
            'posts.image_base_url' => 'https://images.test.local',
            'posts.default_cover_image.url' => '/images/default-cover.png',
        ]);
    }

    private function postWithClientType(string $uri, array $payload)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->postJson($uri, $payload);
    }

    private function getWithClientType(string $uri)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->getJson($uri);
    }

    public function test_meta_returns_article_metadata_for_post_url(): void
    {
        $this->createPublishedPost('Meta Post');

        $this->getWithClientType('/api/v1/meta?url=/posts/meta-post')
            ->assertOk()
            ->assertJsonPath('data.url', '/posts/meta-post')
            ->assertJsonPath('data.resolved_url', '/posts/meta-post')
            ->assertJsonPath('data.canonical_url', '/posts/meta-post')
            ->assertJsonPath('data.title', 'Meta Post')
            ->assertJsonPath('data.description', '본문입니다. 설명으로 사용합니다.')
            ->assertJsonPath('data.image_url', '/images/default-cover.png')
            ->assertJsonPath('data.type', 'article')
            ->assertJsonPath('data.site_name', '테스트 블로그')
            ->assertJsonPath('data.locale', 'ko_KR')
            ->assertJsonPath('data.robots.index', true)
            ->assertJsonPath('data.robots.follow', true)
            ->assertJsonStructure([
                'data' => [
                    'published_time',
                    'modified_time',
                ],
            ]);
    }

    public function test_meta_rejects_external_url(): void
    {
        $this->getWithClientType('/api/v1/meta?url=https://external.test/posts/meta-post')
            ->assertStatus(422);
    }

    public function test_meta_returns_not_found_for_short_url_path(): void
    {
        $this->getWithClientType('/api/v1/meta?url=/s/aB3x9K')
            ->assertNotFound();
    }

    public function test_meta_returns_not_found_for_unknown_post(): void
    {
        $this->getWithClientType('/api/v1/meta?url=/posts/unknown')
            ->assertNotFound();
    }

    private function createPublishedPost(string $title): Post
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => $title,
            'tags' => ['meta'],
            'body' => '본문입니다. 설명으로 사용합니다.',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');
        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])->assertOk();

        return Post::query()->where('uuid', $uuid)->firstOrFail();
    }
}
