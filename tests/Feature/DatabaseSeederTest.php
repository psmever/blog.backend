<?php

namespace Tests\Feature;

use App\Models\CommonCode;
use App\Models\User;
use Database\Seeders\DatabaseSeeder as AppDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_admin_user_when_configured_email_does_not_exist(): void
    {
        User::factory()->create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
        ]);

        $this->setAdminSeedConfig('관리자', 'admin@example.com', 'secret-password');

        $this->seed(AppDatabaseSeeder::class);

        $user = User::query()->where('email', 'admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('관리자', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertDatabaseCount('users', 2);
    }

    public function test_database_seeder_skips_admin_user_creation_when_config_is_missing(): void
    {
        $this->setAdminSeedConfig('', '', '');

        $this->seed(AppDatabaseSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_database_seeder_skips_admin_user_creation_when_configured_email_already_exists(): void
    {
        User::factory()->create([
            'name' => 'Existing Admin',
            'email' => 'admin@example.com',
        ]);

        $this->setAdminSeedConfig('관리자', 'admin@example.com', 'secret-password');

        $this->seed(AppDatabaseSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'name' => 'Existing Admin',
        ]);
    }

    public function test_database_seeder_updates_common_codes_on_every_run(): void
    {
        $this->setAdminSeedConfig('관리자', 'admin@example.com', 'secret-password');

        $this->seed(AppDatabaseSeeder::class);

        $draftStatus = CommonCode::query()
            ->where('group_key', 'post.status')
            ->where('code', 'draft')
            ->firstOrFail();

        $draftStatus->label = '임시 저장 변경';
        $draftStatus->save();
        $draftStatus->delete();

        $this->seed(AppDatabaseSeeder::class);

        $restoredDraftStatus = CommonCode::withTrashed()
            ->where('group_key', 'post.status')
            ->where('code', 'draft')
            ->firstOrFail();

        $this->assertSame('임시 저장', $restoredDraftStatus->label);
        $this->assertNull($restoredDraftStatus->deleted_at);
        $this->assertDatabaseCount('users', 1);
    }

    private function setAdminSeedConfig(string $name, string $email, string $password): void
    {
        config()->set('admin.seed_user.name', $name);
        config()->set('admin.seed_user.email', $email);
        config()->set('admin.seed_user.password', $password);
    }
}
