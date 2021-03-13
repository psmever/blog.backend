<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SectionPosts
 *
 * @property int $id
 * @property int $user_id 사용자 id
 * @property string $post_uuid
 * @property string $gubun 섹션 구분
 * @property string $title
 * @property string $markdown 마크다운 유무.
 * @property string $contents_html
 * @property string $contents_text
 * @property string $publish 게시 유무.
 * @property string $active 글 공개 여부.
 * @property int $view_count 뷰 카운트.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts query()
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereContentsHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereContentsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereGubun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts wherePostUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts wherePublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionPosts whereViewCount($value)
 * @mixin \Eloquent
 */
class SectionPosts extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = "section_posts";

    /**
     * @var string[]
     */
    protected $fillable = ['id', 'user_id', 'post_uuid', 'gubun', 'title', 'markdown', 'contents_html', 'contents_text', 'publish', 'active', 'view_count'];

    // 글 등록자.

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}


