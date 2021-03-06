<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CovidMaster
 *
 * @property int $id
 * @property string $title 타이틀.
 * @property string $gubun
 * @property string $gubun_en
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
 */
class CovidMaster extends Model
{
    use HasFactory;

    protected $table = "covid_master";

    protected $fillable = ['id', 'title', 'gubun', 'gubun_en'];

    // covid data 관계.
    public function covid_state()
    {
        return $this->hasOne(CovidState::class, 'gubun_id' , 'id')->latest();
    }

    // 현재 및 이전 데이터용.
    public function covid_total()
    {
        return $this->hasMany(CovidState::class, 'gubun_id' , 'id')->orderBy('id', 'desc')->take(2);
    }
}
