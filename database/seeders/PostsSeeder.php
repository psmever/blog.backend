<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Posts;
use App\Models\PostsTags;

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
            Posts::factory()->count(1)->create();
            PostsTags::factory()->count(1)->create();
        }
    }
}
