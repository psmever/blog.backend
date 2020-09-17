<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Posts;
use App\User;
use Faker\Generator as Faker;
use App\Supports\Facades\GuitarClass;

$factory->define(Posts::class, function (Faker $faker) {

    $title = $faker->company;
    $text = $faker->text();

    return [
        'user_id' => User::all()->random()->id,
        'post_uuid' => $faker->uuid(),
        'title' => $title,
        'slug_title' => GuitarClass::convertSlugString($title),
        'contents_html' => $text,
        'contents_text' => $text,
        'markdown' => 'Y',
        'post_active' => 'Y',
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ];
});
