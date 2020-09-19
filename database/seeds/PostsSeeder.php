<?php

use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') == "testing") {
            factory('App\Model\Posts', 100)->create();
            factory('App\Model\PostsTags', 50)->create();
        }
    }
}
