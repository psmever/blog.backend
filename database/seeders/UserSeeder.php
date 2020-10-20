<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //테스트 일떄만 생성.
        if(env('APP_ENV') == "testing") {
            DB::table('users')->insert([
                'user_uuid' => Str::uuid()->toString(),
                'name' => Str::random(10),
                'nickname' => Str::random(10),
                'email' => 'root@gmail.com',
                'password' => Hash::make('password'),
                'user_level' => 'S02999',
                'email_verified_at' => \Carbon\Carbon::now(),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            DB::table('users')->insert([
                'user_uuid' => Str::uuid()->toString(),
                'name' => Str::random(10),
                'nickname' => Str::random(10),
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'user_level' => 'S02900',
                'email_verified_at' => \Carbon\Carbon::now(),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            DB::table('users')->insert([
                'user_uuid' => Str::uuid()->toString(),
                'name' => Str::random(10),
                'nickname' => Str::random(10),
                'email' => 'guest@gmail.com',
                'password' => Hash::make('password'),
                'user_level' => 'S02010',
                'email_verified_at' => \Carbon\Carbon::now(),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }
    }
}
