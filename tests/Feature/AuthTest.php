<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
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

    public function test_login_issues_access_and_refresh_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'author@example.com',
            'password' => Hash::make('secret'),
        ]);
        $user->createToken('old-token', ['access-api']);

        $this->postWithClientType('/api/auth/login', [
            'email' => 'author@example.com',
            'password' => 'secret',
        ])
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'access_token_expires_at',
                    'refresh_token',
                    'refresh_token_expires_at',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 2);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->getKey(),
            'name' => 'api-access',
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->getKey(),
            'name' => 'api-refresh',
        ]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->getKey(),
            'name' => 'old-token',
        ]);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'author@example.com',
            'password' => Hash::make('secret'),
        ]);

        $this->postWithClientType('/api/auth/login', [
            'email' => 'author@example.com',
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', '이메일 또는 비밀번호가 올바르지 않습니다.');
    }

    public function test_login_allows_non_email_identifier_in_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        User::factory()->create([
            'email' => 'local',
            'password' => Hash::make('password'),
        ]);

        $this->postWithClientType('/api/auth/login', [
            'email' => 'local',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'local');
    }

    public function test_login_requires_email_format_outside_local_environment(): void
    {
        $this->postWithClientType('/api/auth/login', [
            'email' => 'local',
            'password' => 'password',
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', '요청값이 올바르지 않습니다.')
            ->assertJsonValidationErrors(['email']);
    }

    public function test_refresh_rotates_access_and_refresh_tokens(): void
    {
        User::factory()->create([
            'email' => 'author@example.com',
            'password' => Hash::make('secret'),
        ]);

        $login = $this->postWithClientType('/api/auth/login', [
            'email' => 'author@example.com',
            'password' => 'secret',
        ])->assertOk();

        $this->postWithClientType('/api/auth/refresh', [
            'refresh_token' => $login->json('data.refresh_token'),
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Token refreshed')
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'refresh_token',
                ],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 2);
    }

    public function test_refresh_token_cannot_access_protected_api_routes(): void
    {
        User::factory()->create([
            'email' => 'author@example.com',
            'password' => Hash::make('secret'),
        ]);

        $login = $this->postWithClientType('/api/auth/login', [
            'email' => 'author@example.com',
            'password' => 'secret',
        ])->assertOk();

        $this
            ->withHeader('Client-Type', self::CLIENT_TYPE)
            ->withToken($login->json('data.refresh_token'))
            ->getJson('/api/auth/me')
            ->assertForbidden()
            ->assertJsonPath('message', '접근 토큰이 필요합니다. 다시 로그인해 주세요.');
    }
    public function test_refresh_rejects_expired_refresh_token(): void
    {
        $user = User::factory()->create();
        $refresh = $user->createToken(
            name: 'api-refresh',
            abilities: ['token:refresh'],
            expiresAt: now()->subMinute()
        );

        $this->postWithClientType('/api/auth/refresh', [
            'refresh_token' => $refresh->plainTextToken,
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', '만료된 리프레시 토큰입니다.');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $refresh->accessToken->getKey(),
        ]);
    }

    public function test_logout_revokes_current_access_token(): void
    {
        User::factory()->create([
            'email' => 'author@example.com',
            'password' => Hash::make('secret'),
        ]);

        $login = $this->postWithClientType('/api/auth/login', [
            'email' => 'author@example.com',
            'password' => 'secret',
        ])->assertOk();

        $this
            ->withHeader('Client-Type', self::CLIENT_TYPE)
            ->withToken($login->json('data.access_token'))
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonPath('data.success', true);

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'api-access',
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'api-refresh',
        ]);
    }
}
