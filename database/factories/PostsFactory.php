<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Posts;
use Faker\Generator as Faker;

$factory->define(Posts::class, function (Faker $faker) {
    return [
        'user_id' => 2,
// 'user_id'
// 'post_uuid
// 'title
// 'slug_title
// 'contents_html
// 'contents_text
// 'markdown
// 'post_active
// 'created_at
// 'updated_at
    ];
});
