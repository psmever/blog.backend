<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VilageFcstinfo
 *
 * @property int $id
 * @property int $version_id 버전 id.
 * @property string $gubun 구분.
 * @property string $area_code 행정구역코드.
 * @property string $step1 1단계.
 * @property string $step2 2단계.
 * @property string $step3 3단계.
 * @property string $grid_x 격자 X.
 * @property string $grid_y 격자 Y.
 * @property string $longitude_hour 경도(시).
 * @property string $longitude_minute 경도(분).
 * @property string $longitude_second 경도(초).
 * @property string $latitude_hour 위도(시).
 * @property string $latitude_minute 위도(분).
 * @property string $latitude_second 위도(초).
 * @property string $longitude 경도(초/100).
 * @property string $latitude 위도(초/100).
 * @property string $update_time 위치업데이트.
 * @property string $active 사용 유무.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereGridX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereGridY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereGubun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitudeHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitudeMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitudeSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitudeHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitudeMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitudeSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereStep1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereStep2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereStep3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereVersionId($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\VilageFcstinfoFactory factory(...$parameters)
 */
class VilageFcstinfo extends Model
{
    use HasFactory;

    /**
     * 테이블명.
     * @var string
     */
    protected $table = "vilage_fcstinfo";

    /**
     * fillable
     * @var string[]
     */
    protected $fillable = [
        'id',
        'version_id',
        'gubun',
        'area_code',
        'step1',
        'step2',
        'step3',
        'grid_x',
        'grid_y',
        'longitude_hour',
        'longitude_minute',
        'longitude_second',
        'latitude_hour',
        'latitude_minute',
        'latitude_second',
        'longitude',
        'latitude',
        'update_time',
        'active'
    ];
}
