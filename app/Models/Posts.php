<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|PostsTags[] $tag
 * @property-read int|null $tag_count
 * @property-read PostsThumbs|null $thumb
 * @property-read User|null $user
 * @method static Builder|Posts newModelQuery()
 * @method static Builder|Posts newQuery()
 * @method static Builder|Posts query()
 * @method static Builder|Posts whereContentsHtml($value)
 * @method static Builder|Posts whereContentsText($value)
 * @method static Builder|Posts whereCreatedAt($value)
 * @method static Builder|Posts whereId($value)
 * @method static Builder|Posts whereMarkdown($value)
 * @method static Builder|Posts wherePostActive($value)
 * @method static Builder|Posts wherePostPublish($value)
 * @method static Builder|Posts wherePostUuid($value)
 * @method static Builder|Posts whereSlugTitle($value)
 * @method static Builder|Posts whereTitle($value)
 * @method static Builder|Posts whereUpdatedAt($value)
 * @method static Builder|Posts whereUserId($value)
 * @method static Builder|Posts whereViewCount($value)
 * @mixin Eloquent
 * @method static \Database\Factories\PostsFactory factory(...$parameters)
 */
class Posts extends Model
{
    use HasFactory;

    /**
     * 테이블명.
     *
     * @var string
     */
    protected $table = "posts";

    protected $fillable = ['id', 'title', 'user_id', 'post_uuid', 'contents_html', 'contents_text', 'slug_title', 'post_active', 'post_publish'];

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

    /**
     * 글 등록자.
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * 글 테그.
     * @return HasMany
     */
    public function tag(): HasMany
    {
        return $this->hasMany(PostsTags::class, 'post_id', 'id');
    }

    /**
     * 썸네일
     * @return HasOne
     */
    public function thumb(): HasOne
    {
        return $this->hasOne(PostsThumbs::class, 'post_id', 'id');
    }
}
