<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TruncatePostTablesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_truncate_command_clears_only_post_related_tables_in_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $user = User::factory()->create();
        $now = now();
        $postUuid = (string) Str::uuid();
        $imageUuid = (string) Str::uuid();

        $tagId = DB::table('tags')->insertGetId([
            'key' => 'laravel',
            'label' => 'Laravel',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $postId = DB::table('posts')->insertGetId([
            'uuid' => $postUuid,
            'user_id' => $user->getKey(),
            'title' => 'Test Post',
            'slug' => 'test-post',
            'status' => 'draft',
            'published_at' => null,
            'cover_image_id' => null,
            'body' => 'body',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $imageId = DB::table('post_images')->insertGetId([
            'uuid' => $imageUuid,
            'post_id' => $postId,
            'user_id' => $user->getKey(),
            'post_uuid' => $postUuid,
            'purpose' => 'body',
            'disk' => 'public',
            'path' => 'posts/'.$postUuid.'/body/'.$imageUuid.'.png',
            'url' => 'https://example.test/image.png',
            'original_name' => 'image.png',
            'mime_type' => 'image/png',
            'size' => 1,
            'width' => 1,
            'height' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('posts')
            ->where('id', $postId)
            ->update(['cover_image_id' => $imageId]);

        DB::table('post_tag')->insert([
            'post_id' => $postId,
            'tag_id' => $tagId,
        ]);

        DB::table('post_status_histories')->insert([
            'post_id' => $postId,
            'from_status' => null,
            'to_status' => 'draft',
            'changed_by_user_id' => $user->getKey(),
            'action' => 'create',
            'changed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->artisan('posts:truncate-local')
            ->expectsOutput('Truncated post-related tables: post_status_histories, post_tag, post_images, posts, tags')
            ->assertExitCode(0);

        $this->assertDatabaseCount('post_status_histories', 0);
        $this->assertDatabaseCount('post_tag', 0);
        $this->assertDatabaseCount('post_images', 0);
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_truncate_command_fails_outside_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'testing');

        $this->artisan('posts:truncate-local')
            ->expectsOutput('This command can only be run in the local environment.')
            ->assertExitCode(1);
    }
}
