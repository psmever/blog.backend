<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Supports\Facades\GuitarClass;
use App\Models\Posts;

class PostsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Posts::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->company;
        $text = $this->faker->unique()->text();

        return [
            'user_id' => User::where('user_level', 'S02900')->first()->id,
            'post_uuid' => $this->faker->uuid(),
            'title' => $title,
            'slug_title' => GuitarClass::convertSlugString($title),
            'contents_html' => $text,
            'contents_text' => $text,
            'markdown' => 'Y',
            'post_publish' => 'Y',
            'post_active' => 'Y',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ];
    }
}

