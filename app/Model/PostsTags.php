<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostsTags extends Model
{
    protected $table = "posts_tags";

    protected $fillable = ['post_id', 'tag_id', 'tag_text'];

    // 글 테그.
    public function posts()
    {
        return $this->hasMany(Posts::class, 'id', 'post_id');
    }
}
