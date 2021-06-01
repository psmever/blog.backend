<?php

namespace Database\Factories;

use App\Models\PostsTags;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class PostsTagsFactory
 * @package Database\Factories
 */
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
    public function definition() : array
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
