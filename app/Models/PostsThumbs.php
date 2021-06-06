<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * 테이블명.
     * @var string
     */
    protected $table = "posts_thumbs";

    /**
     * fillable
     * @var string[]
     */
    protected $fillable = [
        'post_id',
        'media_file_id'
    ];

    /**
     * 미디어 파일
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function file(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MediaFiles::class, 'id', 'media_file_id');
    }
}
