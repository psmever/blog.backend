<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\PostsThumbs
 *
 * @property int $id
 * @property int $post_id post id.
 * @property int|null $media_file_id media file table id.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MediaFiles|null $file
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereMediaFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
