<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PostsTags
 *
 * @property int $id
 * @property int $post_id post id.
 * @property string|null $tag_id 테그 id.
 * @property string|null $tag_text 테그 내용.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Posts|null $posts
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereTagText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\PostsTagsFactory factory(...$parameters)
 */
class PostsTags extends Model
{
    use HasFactory;

    /**
     * 테이블명
     * @var string
     */
    protected $table = "posts_tags";

    /**
     * fillable
     * @var string[]
     */
    protected $fillable = [
        'post_id',
        'tag_id',
        'tag_text'
    ];

    /**
     * 글 테그.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Posts::class, 'id', 'post_id');
    }
}
