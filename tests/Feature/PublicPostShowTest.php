<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

class PublicPostShowTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
        config(['app.url' => 'https://api.test.local']);
        config(['posts.image_base_url' => 'https://images.test.local']);
    }

    private function postWithClientType(string $uri, array $payload)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->postJson($uri, $payload);
    }

    private function getWithClientType(string $uri)
    {
        return $this->withHeader('Client-Type', self::CLIENT_TYPE)->getJson($uri);
    }

    public function test_public_show_returns_published_post_and_increments_view_count_once_per_session(): void
    {
        $author = User::factory()->create(['name' => '공개 작성자']);
        $post = $this->createPublishedPost($author, 'Public Detail', ['Next.js', 'React']);

        $this->app['auth']->forgetGuards();

        $first = $this->getWithClientType('/api/v1/public/posts/public-detail')->assertOk();
        $first
            ->assertJsonPath('data.slug', 'public-detail')
            ->assertJsonPath('data.author.name', '공개 작성자')
            ->assertJsonPath('data.tags.0.key', 'next-js')
            ->assertJsonPath('data.tags.1.key', 'react')
            ->assertJsonPath('data.cover_image.uuid', null)
            ->assertJsonPath('data.cover_image.purpose', 'default')
            ->assertJsonPath('data.cover_image.url', 'https://images.test.local/images/default-cover.png')
            ->assertJsonPath('data.cover_image.width', 1200)
            ->assertJsonPath('data.cover_image.height', 630)
            ->assertJsonPath('data.cover_image.size', 0)
            ->assertJsonPath('data.cover_image.is_default', true)
            ->assertJsonPath('data.cover_image.thumbnail', null)
            ->assertJsonPath('data.view_count', 1)
            ->assertJsonPath('data.body', '# markdown 원문');

        $this->assertDatabaseHas('posts', [
            'id' => $post->getKey(),
            'view_count' => 1,
        ]);

        $sessionCookie = $this->sessionCookieFrom($first->headers->getCookies());
        $this->assertNotNull($sessionCookie);

        $this->withCredentials()
            ->withUnencryptedCookie($sessionCookie->getName(), (string) $sessionCookie->getValue())
            ->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/public/posts/public-detail')
            ->assertOk()
            ->assertJsonPath('data.view_count', 1);

        $this->assertDatabaseHas('posts', [
            'id' => $post->getKey(),
            'view_count' => 1,
        ]);

        $cookieName = (string) config('session.cookie');
        $this->defaultCookies = [];
        $this->unencryptedCookies = [];
        $this->withCredentials = false;
        $session = $this->app['session.store'];
        $session->flush();
        $session->setId('fresh-public-session');

        $this->withCredentials()
            ->withCookie($cookieName, 'fresh-public-session')
            ->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/public/posts/public-detail')
            ->assertOk()
            ->assertJsonPath('data.view_count', 2);

        $this->assertDatabaseHas('posts', [
            'id' => $post->getKey(),
            'view_count' => 2,
        ]);
    }

    public function test_public_show_returns_not_found_for_draft_or_unknown_slug(): void
    {
        $author = User::factory()->create();
        Sanctum::actingAs($author);

        $this->postWithClientType('/api/v1/posts', [
            'title' => 'Draft Detail',
            'tags' => ['draft'],
            'body' => 'draft body',
        ])->assertCreated();

        $this->app['auth']->forgetGuards();

        $this->getWithClientType('/api/v1/public/posts/draft-detail')
            ->assertNotFound();

        $this->getWithClientType('/api/v1/public/posts/unknown-post')
            ->assertNotFound();
    }

    public function test_public_show_accepts_encoded_and_double_encoded_korean_slug(): void
    {
        $author = User::factory()->create();
        $this->createPublishedPost($author, '슬러그 테스트', ['laravel']);

        $this->app['auth']->forgetGuards();

        $encodedSlug = rawurlencode('슬러그-테스트');

        $this->getWithClientType('/api/v1/public/posts/'.$encodedSlug)
            ->assertOk()
            ->assertJsonPath('data.slug', '슬러그-테스트');

        $this->getWithClientType('/api/v1/public/posts/'.rawurlencode($encodedSlug))
            ->assertOk()
            ->assertJsonPath('data.slug', '슬러그-테스트');
    }

    public function test_public_show_returns_absolute_default_cover_image_url(): void
    {
        config([
            'posts.default_cover_image.url' => 'https://cdn.jaubi.co.kr/blog/assets/default-cover.png',
        ]);

        $author = User::factory()->create();
        $this->createPublishedPost($author, 'CDN Cover Detail', ['cdn']);

        $this->app['auth']->forgetGuards();

        $this->getWithClientType('/api/v1/public/posts/cdn-cover-detail')
            ->assertOk()
            ->assertJsonPath('data.cover_image.url', 'https://cdn.jaubi.co.kr/blog/assets/default-cover.png')
            ->assertJsonPath('data.cover_image.is_default', true)
            ->assertJsonPath('data.cover_image.thumbnail', null);
    }

    private function createPublishedPost(User $user, string $title, array $tags): Post
    {
        Sanctum::actingAs($user);

        $created = $this->postWithClientType('/api/v1/posts', [
            'title' => $title,
            'tags' => $tags,
            'body' => '# markdown 원문',
        ])->assertCreated();

        $uuid = (string) $created->json('data.uuid');

        $this->postWithClientType('/api/v1/posts/'.$uuid.'/publish', [])->assertOk();

        return Post::query()->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * @param  array<int, Cookie>  $cookies
     */
    private function sessionCookieFrom(array $cookies): ?Cookie
    {
        $cookieName = (string) config('session.cookie');

        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $cookieName) {
                return $cookie;
            }
        }

        return null;
    }
}
