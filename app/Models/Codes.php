<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Codes
 *
 * @property int $id
 * @property string $group 그룹
 * @property string|null $code 코드
 * @property string|null $group_name 그룹명
 * @property string|null $code_name 코드 네임
 * @property string $active 사용 상태(사용중, 비사용)
 * @property Carbon $created_at
 * @method static Builder|Codes newModelQuery()
 * @method static Builder|Codes newQuery()
 * @method static Builder|Codes query()
 * @method static Builder|Codes whereActive($value)
 * @method static Builder|Codes whereCode($value)
 * @method static Builder|Codes whereCodeName($value)
 * @method static Builder|Codes whereCreatedAt($value)
 * @method static Builder|Codes whereGroup($value)
 * @method static Builder|Codes whereGroupName($value)
 * @method static Builder|Codes whereId($value)
 * @mixin Eloquent
 */
class Codes extends Model
{
	use HasFactory;

	const UPDATED_AT = null;

	/**
	 * @var string[]
	 */
	protected $fillable = [
		'group',
		'code',
		'group_name',
		'code_name'
	];
}
