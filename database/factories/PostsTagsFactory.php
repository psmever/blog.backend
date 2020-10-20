<?php

namespace Database\Factories;

use App\Models\PostsTags;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Supports\Facades\GuitarClass;

class PostsTagsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostsTags::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $posts =  Posts::select("id")->inRandomOrder()->first();

        $kakerString = $this->faker->word;

        return [
            'post_id' => $posts->id,
            'tag_id' => $kakerString,
            'tag_text' => $kakerString,
        ];
    }
}

