<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CommonCodeSeeder::class,
        ]);

        if (app()->environment(['local', 'development'])) {
            $email = env('SEED_TEST_USER_EMAIL', 'test@example.com');
            $user = User::query()->firstOrNew(['email' => $email]);
            $user->name = env('SEED_TEST_USER_NAME', 'Test User');
            $user->email_verified_at = now();
            $user->password = env('SEED_TEST_USER_PASSWORD', 'password');
            $user->remember_token = Str::random(10);
            $user->save();
        }
    }
}
