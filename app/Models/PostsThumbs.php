<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostsThumbs extends Model
{
    use HasFactory;

    protected $table = "posts_thumbs";

    protected $fillable = ['post_id', 'media_file_id'];

    // 미디어 파일
    public function file()
    {
        return $this->hasOne(MediaFiles::class, 'id', 'media_file_id');
    }
}
