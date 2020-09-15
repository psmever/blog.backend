<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Posts extends Model
{
    use Sluggable;

    protected $table = "posts";

    protected $fillable = ['title', 'user_id', 'post_uuid', 'contents_html', 'contents_text'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable() : array
    {
        return [
            'slug_title' => [
                'source' => 'title'
            ]
        ];
    }
}
