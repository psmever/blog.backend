<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Posts
 *
 * @property int $id
 * @property int $user_id 사용자 id
 * @property string $uuid 고유 id.
 * @property string $title 제목.
 * @property string $slug slug 타이틀.
 * @property string $contents 내용(마크다운).
 * @property string $contents_html 내용(html).
 * @property string $publish 게시 유무.
 * @property int $view 뷰 카운트.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Posts newModelQuery()
 * @method static Builder|Posts newQuery()
 * @method static Builder|Posts query()
 * @method static Builder|Posts whereContents($value)
 * @method static Builder|Posts whereContentsHtml($value)
 * @method static Builder|Posts whereCreatedAt($value)
 * @method static Builder|Posts whereDeletedAt($value)
 * @method static Builder|Posts whereId($value)
 * @method static Builder|Posts wherePublish($value)
 * @method static Builder|Posts whereSlugTitle($value)
 * @method static Builder|Posts whereTitle($value)
 * @method static Builder|Posts whereUpdatedAt($value)
 * @method static Builder|Posts whereUserId($value)
 * @method static Builder|Posts whereUuid($value)
 * @method static Builder|Posts whereView($value)
 * @mixin Eloquent
 */
class Posts extends Model
{
	use HasFactory, Sluggable;

	protected $fillable = [
		'user_id',
		'uuid',
		'title',
		'slug',
		'contents',
		'contents_html',
		'publish',
		'view',
	];

	/**
	 * @return array[]
	 */
	public function sluggable(): array
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}
}
