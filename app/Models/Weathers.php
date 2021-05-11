<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Weathers
 *
 * @property int $id
 * @property int $area_code_id 행정구역코드 ID.
 * @property string $fcstDate 예측일자.
 * @property string $fcstTime 예측시간.
 * @property string $T1H 기온.
 * @property string $RN1 1시간 강수량.
 * @property string $SKY 하늘상태(맑음(1), 구름많음(3), 흐림(4))
 * @property string $UUU 동서바람성분.
 * @property string $VVV 남북바람성분.
 * @property string $REH 습도.
 * @property string $PTY 강수형태(없음(0), 비(1), 비/눈(2), 눈(3), 소나기(4), 빗방울(5), 빗방울/눈날림(6), 눈날림(7))
 * @property string $LGT 낙뢰.
 * @property string $VEC 풍향.
 * @property string $WSD 풍속.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VilageFcstinfo $vilage
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers query()
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereAreaCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereFcstDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereFcstTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereLGT($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers wherePTY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereREH($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereRN1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereSKY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereT1H($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereUUU($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereVEC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereVVV($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereWSD($value)
 * @mixin \Eloquent
 */
class Weathers extends Model
{
    use HasFactory;

    /**
     * 테이블 명
     * @var string
     */
    protected $table = "weathers";

    /**
     * fillable
     * @var string[]
     */
    protected $fillable = [
        'area_code_id',
        'fcstDate',
        'fcstTime',
        'T1H',
        'RN1',
        'SKY',
        'UUU',
        'VVV',
        'REH',
        'PTY',
        'LGT',
        'VEC',
        'WSD'
    ];

    /**
     * Get the vilage that owns the Weathers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vilage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\VilageFcstinfo', 'area_code_id', 'id')->select('id', 'gubun', 'area_code', 'step1', 'step2', 'step3');
    }
}
