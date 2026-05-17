<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostListTest extends TestCase
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

    private function getWithClientType(string $uri)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->getJson($uri);
    }

    public function test_list_published_posts_for_current_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $published = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Published Post',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();
        $publishedUuid = (string) $published->json('data.uuid');
        $this->postWithClientType('/api/v1/posts/'.$publishedUuid.'/publish', [])->assertOk();

        $draft = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Draft Post',
            'tags' => ['laravel'],
            'body' => 'body',
        ])->assertCreated();
        $draftUuid = (string) $draft->json('data.uuid');

        $other = User::factory()->create();
        Sanctum::actingAs($other);
        $otherPost = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Other Published Post',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();
        $otherUuid = (string) $otherPost->json('data.uuid');
        $this->postWithClientType('/api/v1/posts/'.$otherUuid.'/publish', [])->assertOk();

        Sanctum::actingAs($user);
        $this->getWithClientType('/api/v1/posts?status=published&limit=20')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.uuid', $publishedUuid)
            ->assertJsonPath('data.0.title', 'Published Post')
            ->assertJsonPath('data.0.status', 'published')
            ->assertJsonMissing(['uuid' => $draftUuid])
            ->assertJsonMissing(['uuid' => $otherUuid]);
    }

    public function test_list_published_posts_returns_empty_array(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getWithClientType('/api/v1/posts?status=published')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_list_draft_posts_for_current_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $draft = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Draft Post',
            'tags' => ['laravel'],
            'body' => 'body',
        ])->assertCreated();
        $draftUuid = (string) $draft->json('data.uuid');

        $published = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Published Post',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();
        $publishedUuid = (string) $published->json('data.uuid');
        $this->postWithClientType('/api/v1/posts/'.$publishedUuid.'/publish', [])->assertOk();

        $this->getWithClientType('/api/v1/posts?status=draft&limit=20')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.uuid', $draftUuid)
            ->assertJsonPath('data.0.title', 'Draft Post')
            ->assertJsonPath('data.0.status', 'draft')
            ->assertJsonMissing(['uuid' => $publishedUuid]);
    }

    public function test_list_published_posts_requires_authentication(): void
    {
        $this->getWithClientType('/api/v1/posts?status=published')
            ->assertUnauthorized();
    }

    public function test_list_published_posts_validates_limit(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getWithClientType('/api/v1/posts?status=published&limit=51')
            ->assertUnprocessable();
    }
}
