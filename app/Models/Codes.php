<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Codes
 *
 * @property int $id
 * @property string $group_id
 * @property string|null $code_id
 * @property string|null $group_name
 * @property string|null $code_name
 * @property string $active 사용 상태(사용중, 비사용)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Codes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Codes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Codes query()
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereCodeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Codes extends Model
{
    use HasFactory;

    /**
     * 공통 코드 테이블
     * @var string
     */
    protected $table = "codes";

    /**
     * fillable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'group_id',
        'code_id',
        'group_name',
        'code_name'
    ];
}
