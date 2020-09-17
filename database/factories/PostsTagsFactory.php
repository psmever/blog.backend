<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\PostsTags;
use App\Model\Posts;
use Faker\Generator as Faker;

$factory->define(PostsTags::class, function (Faker $faker) {

    $posts =  Posts::select("id")->orderBy("id", "desc")->first();

    $kakerString = $faker->word;
    return [
        'post_id' => $posts->id,
        'tag_id' => $kakerString,
        'tag_text' => $kakerString,
    ];
});
