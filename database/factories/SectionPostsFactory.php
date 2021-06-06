<?php

namespace Database\Factories;

use App\Models\SectionPosts;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SectionPostsFactory
 * @package Database\Factories
 */
class SectionPostsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SectionPosts::class;

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
            'contents_html' => $text,
            'contents_text' => $text,
            'markdown' => 'Y',
            'publish' => 'Y',
            'active' => 'Y',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
