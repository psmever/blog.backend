<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Supports\Facades\GuitarClass;

/**
 * App\Models\Posts
 *
 * @property int $id
 * @property int $user_id 사용자 id
 * @property string $post_uuid
 * @property string $title
 * @property string $slug_title
 * @property string $contents_html
 * @property string $contents_text
 * @property string $markdown 마크다운 유무.
 * @property string $post_publish 게시 유무.
 * @property string $post_active 글 공개 여부.
 * @property int $view_count 뷰 카운트.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostsTags[] $tag
 * @property-read int|null $tag_count
 * @property-read \App\Models\PostsThumbs|null $thumb
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Posts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts query()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereContentsHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereContentsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts wherePostActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts wherePostPublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts wherePostUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereSlugTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereViewCount($value)
 * @mixin \Eloquent
 */
class Posts extends Model
{
    use HasFactory;

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
        return $this->hasOne(User::class, 'id', 'user_id');
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
