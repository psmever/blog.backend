<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weathers extends Model
{
    use HasFactory;

    protected $table = "weathers";

    protected $fillable = ['area_code_id', 'fcstDate', 'fcstTime', 'T1H', 'RN1', 'SKY', 'UUU', 'VVV', 'REH', 'PTY', 'LGT', 'VEC', 'WSD'];

    protected $dateFormat = 'U';

    /**
     * Get the vilage that owns the Weathers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vilage()
    {
        return $this->belongsTo('App\Models\VilageFcstinfo', 'area_code_id', 'id')->select('id', 'gubun', 'area_code', 'step1', 'step2', 'step3');
    }
}


