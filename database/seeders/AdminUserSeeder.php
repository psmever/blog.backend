<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's admin user when the configured admin email does not exist yet.
     */
    public function run(): void
    {
        $adminUser = $this->adminUserAttributes();

        if ($adminUser === null) {
            return;
        }

        $adminEmailExists = User::query()
            ->where('email', $adminUser['email'])
            ->exists();

        if ($adminEmailExists) {
            return;
        }

        $user = new User;
        $user->name = $adminUser['name'];
        $user->email = $adminUser['email'];
        $user->email_verified_at = now();
        $user->password = $adminUser['password'];
        $user->remember_token = Str::random(10);
        $user->save();
    }

    /**
     * @return array{name: string, email: string, password: string}|null
     */
    private function adminUserAttributes(): ?array
    {
        $name = trim((string) config('admin.seed_user.name', ''));
        $email = trim((string) config('admin.seed_user.email', ''));
        $password = (string) config('admin.seed_user.password', '');

        if ($name === '' || $email === '' || trim($password) === '') {
            return null;
        }

        return [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];
    }
}
