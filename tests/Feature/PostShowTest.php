<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostShowTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
        config(['app.url' => 'https://api.test.local']);
    }

    private function postWithClientType(string $uri, array $payload)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->postJson($uri, $payload);
    }

    private function getWithClientType(string $uri)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->getJson($uri);
    }

    public function test_show_post_by_uuid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $create = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Hello React',
            'tags' => ['react', 'Next.js'],
            'body' => '본문 내용',
        ])->assertCreated();

        $uuid = (string) $create->json('data.uuid');

        $response = $this->getWithClientType('/api/v1/posts/'.$uuid);
        $response->assertOk();
        $response->assertJsonPath('data.uuid', $uuid);
        $response->assertJsonPath('data.title', 'Hello React');
        $response->assertJsonPath('data.slug', 'hello-react');
        $response->assertJsonPath('data.cover_image.uuid', null);
        $response->assertJsonPath('data.cover_image.purpose', 'default');
        $response->assertJsonPath('data.cover_image.url', 'https://api.test.local/images/default-cover.png');
        $response->assertJsonPath('data.cover_image.width', 1200);
        $response->assertJsonPath('data.cover_image.height', 630);
        $response->assertJsonPath('data.cover_image.size', 0);
        $response->assertJsonPath('data.cover_image.is_default', true);
        $response->assertJsonPath('data.body', '본문 내용');
        $response->assertJsonCount(2, 'data.tags');
    }

    public function test_show_post_by_uuid_returns_not_found_for_other_user(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);

        $create = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Owner Post',
            'tags' => ['react'],
            'body' => 'secret',
        ])->assertCreated();

        $uuid = (string) $create->json('data.uuid');

        $other = User::factory()->create();
        Sanctum::actingAs($other);

        $this->getWithClientType('/api/v1/posts/'.$uuid)
            ->assertNotFound()
            ->assertJsonPath('message', '게시글을 찾을 수 없습니다.');
    }
}
