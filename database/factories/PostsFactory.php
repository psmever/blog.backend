<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Supports\Facades\GuitarClass;
use App\Models\Posts;

/**
 * Class PostsFactory
 * @package Database\Factories
 */
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
    public function definition() : array
    {
        $text = $this->faker->unique()->text();
        $title = $this->faker->unique()->company;

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
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
