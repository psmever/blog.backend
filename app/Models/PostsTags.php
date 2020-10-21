<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostsTags extends Model
{
    use HasFactory;

    protected $table = "posts_tags";

    protected $fillable = ['post_id', 'tag_id', 'tag_text'];

    // 글 테그.
    public function posts()
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
}
