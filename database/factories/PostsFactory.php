<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Posts;
use App\User;
use Faker\Generator as Faker;
use App\Supports\Facades\GuitarClass;

$factory->define(Posts::class, function (Faker $faker) {

    $title = $faker->unique()->company;
    $text = $faker->unique()->text();

    return [
        'user_id' => User::where('user_level', 'S02900')->first()->id,
        'post_uuid' => $faker->uuid(),
        'title' => $title,
        'slug_title' => GuitarClass::convertSlugString($title),
        'contents_html' => $text,
        'contents_text' => $text,
        'markdown' => 'Y',
        'publish' => array('Y', 'N')[rand(0,1)],
        'post_active' => array('Y', 'N')[rand(0,1)],
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ];
});
