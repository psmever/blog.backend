<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PublicPostListTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
        config(['app.url' => 'https://api.test.local']);
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
    }

    private function postWithClientType(string $uri, array $payload)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->postJson($uri, $payload);
    }

    private function getWithClientType(string $uri)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->getJson($uri);
    }

    public function test_public_list_returns_published_posts_with_cursor_meta_and_feed_fields(): void
    {
        $author = User::factory()->create(['name' => '홍길동']);
        Sanctum::actingAs($author);

        $featured = $this->createPublishedPostWithCoverImage($author, 'Featured Post', ['React', 'Next.js']);

        foreach (range(1, 12) as $index) {
            $this->createPublishedPost(
                $author,
                sprintf('Published Post %02d', $index),
                ['Laravel'],
                Carbon::parse('2026-05-08 10:00:00')->subMinutes($index)
            );
        }

        $this->postWithClientType('/api/v1/posts', [
            'title' => 'Draft Post',
            'tags' => ['draft'],
            'body' => 'draft body',
        ])->assertCreated();

        $this->app['auth']->forgetGuards();

        $response = $this->getWithClientType('/api/v1/public/posts')->assertOk();

        $response
            ->assertJsonPath('meta.limit', 12)
            ->assertJsonPath('meta.has_more', true)
            ->assertJsonCount(12, 'data')
            ->assertJsonPath('data.0.slug', 'featured-post')
            ->assertJsonPath('data.0.author.name', '홍길동')
            ->assertJsonPath('data.0.primary_tag.key', 'next-js')
            ->assertJsonPath('data.0.primary_tag.label', 'Next.js')
            ->assertJsonPath('data.0.cover_image.uuid', $featured->coverImage?->uuid)
            ->assertJsonPath('data.0.cover_image.is_default', false)
            ->assertJsonPath('data.1.cover_image.uuid', null)
            ->assertJsonPath('data.1.cover_image.purpose', 'default')
            ->assertJsonPath('data.1.cover_image.url', 'https://api.test.local/images/default-cover.png')
            ->assertJsonPath('data.1.cover_image.width', 1200)
            ->assertJsonPath('data.1.cover_image.height', 630)
            ->assertJsonPath('data.1.cover_image.size', 0)
            ->assertJsonPath('data.1.cover_image.is_default', true)
            ->assertJsonPath('data.0.view_count', 0);

        $this->assertSame(
            '본문 첫 문단입니다. 자세한 설명은 React 문서에서 확인하세요.',
            $response->json('data.0.excerpt')
        );
        $this->assertNotNull($response->json('meta.next_cursor'));

        $timestamps = collect($response->json('data'))->pluck('published_at')->all();
        $sorted = $timestamps;
        rsort($sorted);
        $this->assertSame($sorted, $timestamps);
    }

    public function test_public_list_accepts_cursor_for_next_page(): void
    {
        $author = User::factory()->create(['name' => '페이지 작성자']);
        Sanctum::actingAs($author);

        foreach (range(1, 14) as $index) {
            $this->createPublishedPost(
                $author,
                sprintf('Cursor Post %02d', $index),
                ['Cursor'],
                Carbon::parse('2026-05-08 11:00:00')->subMinutes($index)
            );
        }

        $this->app['auth']->forgetGuards();

        $first = $this->getWithClientType('/api/v1/public/posts?limit=12')->assertOk();
        $cursor = (string) $first->json('meta.next_cursor');

        $second = $this->getWithClientType('/api/v1/public/posts?limit=12&cursor='.$cursor)->assertOk();

        $second
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.has_more', false)
            ->assertJsonPath('meta.next_cursor', null);

        $firstSlugs = collect($first->json('data'))->pluck('slug')->all();
        $secondSlugs = collect($second->json('data'))->pluck('slug')->all();

        $this->assertSame([], array_intersect($firstSlugs, $secondSlugs));
    }

    public function test_public_list_validates_limit(): void
    {
        $this->getWithClientType('/api/v1/public/posts?limit=51')
            ->assertUnprocessable();
    }

    private function createPublishedPost(User $user, string $title, array $tags, Carbon $publishedAt): Post
    {
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => $title,
            'tags' => $tags,
            'body' => sprintf('%s 본문입니다.', $title),
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])->assertOk();

        /** @var Post $post */
        $post = Post::query()->where('uuid', $uuid)->firstOrFail();
        $post->forceFill(['published_at' => $publishedAt])->save();

        return $post->refresh()->load(['coverImage', 'tags', 'user']);
    }

    private function createPublishedPostWithCoverImage(User $user, string $title, array $tags): Post
    {
        Sanctum::actingAs($user);

        $issued = $this->postWithClientType('/api/v1/posts/uuid', [])->assertOk();
        $postUuid = (string) $issued->json('data.uuid');

        $upload = $this->withHeader('Client-Type', self::CLIENT_TYPE)
            ->post('/api/v1/posts/'.$postUuid.'/images', [
                'image' => $this->fakePng('featured.png'),
            ])
            ->assertCreated();

        $imageUrl = (string) $upload->json('data.url');

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/save', [
            'title' => $title,
            'tags' => $tags,
            'body' => "본문 첫 문단입니다.\n\n![featured]($imageUrl)\n\n자세한 설명은 [React 문서](https://react.dev)에서 확인하세요.",
        ])->assertOk();

        $this->postWithClientType('/api/v1/posts/'.$postUuid.'/publish', [])->assertOk();

        /** @var Post $post */
        $post = Post::query()->where('uuid', $postUuid)->firstOrFail();
        $post->forceFill(['published_at' => Carbon::parse('2026-05-08 12:00:00')])->save();

        return $post->refresh()->load(['coverImage', 'tags', 'user']);
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=') ?: ''
        );
    }
}
