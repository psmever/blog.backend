<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostSaveTest extends TestCase
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

    public function test_save_sets_draft_and_clears_published_at(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => 'My Post',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])
            ->assertOk();

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/save', [
            'body' => 'saved body',
        ])->assertOk()
            ->assertJsonPath('data.uuid', $uuid);

        $this->assertDatabaseHas('posts', [
            'uuid' => $uuid,
            'status' => 'draft',
            'published_at' => null,
            'body' => 'saved body',
        ]);

        $this->assertDatabaseHas('post_status_histories', [
            'from_status' => 'published',
            'to_status' => 'draft',
            'changed_by_user_id' => $user->getKey(),
            'action' => 'save',
        ]);
    }

    public function test_save_records_history_when_status_is_unchanged(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => 'Draft Post',
            'tags' => ['react'],
            'body' => 'body',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/save', [
            'body' => 'updated body',
        ])->assertOk();

        $this->assertDatabaseHas('post_status_histories', [
            'from_status' => 'draft',
            'to_status' => 'draft',
            'changed_by_user_id' => $user->getKey(),
            'action' => 'save',
        ]);
    }
}
