<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostsThumbs extends Model
{
    protected $table = "posts_thumbs";

    protected $fillable = ['post_id', 'media_file_id'];
}
