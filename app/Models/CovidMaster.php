<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CovidMaster
 *
 * @property int $id
 * @property string $title 타이틀.
 * @property string $gubun 구분 값(한글명).
 * @property string $gubun_en 구분 값(영문명).
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CovidState|null $covid_state
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CovidState[] $covid_total
 * @property-read int|null $covid_total_count
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster whereGubun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster whereGubunEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidMaster whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\CovidMasterFactory factory(...$parameters)
 */
class CovidMaster extends Model
{
    use HasFactory;

    /**
     * 테이블명
     * @var string
     */
    protected $table = "covid_master";

    /**
     * fillable
     * @var string[]
     */
    protected $fillable = ['id', 'title', 'gubun', 'gubun_en'];

    /**
     * covid data 관계.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function covid_state()
    {
        return $this->hasOne(CovidState::class, 'gubun_id' , 'id')->latest();
    }

    /**
     * 현재 및 이전 데이터용.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function covid_total()
    {
        return $this->hasMany(CovidState::class, 'gubun_id' , 'id')->orderBy('id', 'desc')->take(2);
    }
}
