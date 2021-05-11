<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CovidState
 *
 * @property int $id
 * @property int $gubun_id gubun id
 * @property int $seq 발행현황 고유값
 * @property string $createdt 등록일시분초.
 * @property string $deathcnt 사망자수.
 * @property string $incdec 전일대비 증감 수.
 * @property string $isolclearcnt 격리 해제 수.
 * @property string $qurrate 10만명당 발생률.
 * @property string $stdday 기준일시.
 * @property string $updatedt 수정일시분초.
 * @property string $defcnt 확진자 수.
 * @property string $isolingcnt 격리중 환자수.
 * @property string $overflowcnt 해외유입 수.
 * @property string $localocccnt 지역발생 수.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState query()
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereCreatedt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereDeathcnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereDefcnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereGubunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereIncdec($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereIsolclearcnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereIsolingcnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereLocalocccnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereOverflowcnt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereQurrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereStdday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CovidState whereUpdatedt($value)
 * @mixin \Eloquent
 */
class CovidState extends Model
{
    use HasFactory;

    /**
     * 테이블 명.
     *
     * @var string
     */
    protected $table = "covid_state";

    /**
     * fillable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'gubun_id',
        'seq',
        'createdt',
        'deathcnt',
        'incdec',
        'isolclearcnt',
        'qurrate',
        'stdday',
        'updatedt',
        'defcnt',
        'isolingcnt',
        'overflowcnt',
        'localocccnt'
    ];
}
