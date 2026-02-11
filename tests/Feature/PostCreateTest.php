<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostCreateTest extends TestCase
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
        return $this
            ->withHeader('Client-Type', self::CLIENT_TYPE)
            ->postJson($uri, $payload);
    }

    public function test_create_post_with_tags_and_slug(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'Hello React',
            'tags' => ['react', 'Next.js', 'REACT'],
            'body' => '본문 내용',
        ];

        $response = $this->postWithClientType('/api/v1/posts', $payload);

        $response->assertCreated();
        $response->assertJsonStructure(['data' => ['uuid']]);
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/i', (string) $response->json('data.uuid'));

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->getKey(),
            'title' => 'Hello React',
            'slug' => 'hello-react',
        ]);

        $this->assertDatabaseHas('tags', [
            'key' => 'react',
            'label' => 'React',
        ]);
        $this->assertDatabaseHas('tags', [
            'key' => 'next-js',
            'label' => 'Next.js',
        ]);
    }

    public function test_slug_is_unique_per_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'Same Title',
            'tags' => ['react'],
            'body' => '내용',
        ];

        $this->postWithClientType('/api/v1/posts', $payload)->assertCreated();
        $this->postWithClientType('/api/v1/posts', $payload)->assertCreated();

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->getKey(),
            'slug' => 'same-title',
        ]);
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->getKey(),
            'slug' => 'same-title-2',
        ]);

        $other = User::factory()->create();
        Sanctum::actingAs($other);
        $this->postWithClientType('/api/v1/posts', $payload)->assertCreated();

        $this->assertDatabaseHas('posts', [
            'user_id' => $other->getKey(),
            'slug' => 'same-title',
        ]);
    }
}
