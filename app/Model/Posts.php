<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Supports\Facades\GuitarClass;

class Posts extends Model
{
    protected $table = "posts";

    // protected $primaryKey = 'slug_title';

    protected $fillable = ['id', 'title', 'user_id', 'post_uuid', 'contents_html', 'contents_text', 'slug_title'];

    /**
     * Slug Title 처리.
     *
     * @param String $text
     * @return string
     */
    public function slugify(String $text) : string
    {
        # remove ? mark from string
        $slug = GuitarClass::convertSlugString($text);

        # Slug Unique 체크
        # Unit Test 시 에러 방지.
        if(DB::getDriverName() == 'mysql') {
            $latest = $this->whereRaw("slug_title REGEXP '^{$slug}(-[0-9]+)?$'")
            ->latest('id')
            ->value('slug_title');
        } else {
            $latest = $this->whereRaw("slug_title = '^{$slug}(-[0-9]+)?$'")
            ->latest('id')
            ->value('slug_title');
        }

        if($latest){
            $pieces = explode('-', $latest);
            $number = intval(end($pieces));
            $slug .= '-' . ($number + 1);
        }

        return $slug;
    }

    // 글 등록자.
    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }

    // 글 테그.
    public function tag()
    {
        return $this->hasMany(PostsTags::class, 'post_id', 'id');
    }

    // 썸네일
    public function thumb()
    {
        return $this->hasOne(PostsThumbs::class, 'post_id', 'id');
    }
}
