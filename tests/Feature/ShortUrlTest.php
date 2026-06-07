<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShortUrlTest extends TestCase
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

    public function test_authenticated_user_can_create_short_url_with_path_only_response(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postWithClientType('/api/v1/short-urls', [
            'original_url' => '/posts/my-post',
        ])->assertCreated();

        $code = (string) $response->json('data.code');

        $response
            ->assertJsonPath('data.short_url', '/s/'.$code)
            ->assertJsonPath('data.original_url', '/posts/my-post');

        $this->assertDatabaseHas('short_urls', [
            'code' => $code,
            'original_url' => '/posts/my-post',
            'created_by' => $user->getKey(),
        ]);
    }

    public function test_short_url_creation_reuses_existing_url(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $first = $this->postWithClientType('/api/v1/short-urls', [
            'original_url' => '/posts/my-post',
        ])->assertCreated();

        $second = $this->postWithClientType('/api/v1/short-urls', [
            'original_url' => '/posts/my-post',
        ])->assertCreated();

        $this->assertSame($first->json('data.code'), $second->json('data.code'));
        $this->assertDatabaseCount('short_urls', 1);
    }

    public function test_short_url_creation_rejects_external_or_short_url_paths(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postWithClientType('/api/v1/short-urls', [
            'original_url' => 'https://external.test/posts/my-post',
        ])->assertStatus(422);

        $this->postWithClientType('/api/v1/short-urls', [
            'original_url' => '//external.test/posts/my-post',
        ])->assertStatus(422);

        $this->postWithClientType('/api/v1/short-urls', [
            'original_url' => '/s/aB3x9K',
        ])->assertStatus(422);
    }

    public function test_short_url_show_returns_original_url(): void
    {
        ShortUrl::query()->create([
            'code' => 'aB3x9K',
            'original_url' => '/posts/my-post',
        ]);

        $this->getWithClientType('/api/v1/short-urls/aB3x9K')
            ->assertOk()
            ->assertJsonPath('data.code', 'aB3x9K')
            ->assertJsonPath('data.short_url', '/s/aB3x9K')
            ->assertJsonPath('data.original_url', '/posts/my-post');
    }

    public function test_short_url_show_accepts_any_active_client_type_code(): void
    {
        ShortUrl::query()->create([
            'code' => 'aB3x9K',
            'original_url' => '/posts/my-post',
        ]);

        $this->withHeader('Client-Type', 'CT03Z')
            ->getJson('/api/v1/short-urls/aB3x9K')
            ->assertOk()
            ->assertJsonPath('data.code', 'aB3x9K');
    }
}
