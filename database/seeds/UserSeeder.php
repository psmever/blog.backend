<?php

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
        DB::table('users')->insert([
            'user_uuid' => Str::random(50),
            'name' => Str::random(10),
            'nickname' => Str::random(10),
            'email' => 'test@gmail.com',
            'password' => Hash::make('1212'),
            'email_verified_at' => \Carbon\Carbon::now(),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}
