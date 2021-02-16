<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weathers extends Model
{
    use HasFactory;

    protected $table = "weathers";

    protected $fillable = ['area_code_id', 'fcstDate', 'fcstTime', 'T1H', 'RN1', 'SKY', 'UUU', 'VVV', 'REH', 'PTY', 'LGT', 'VEC', 'WSD'];

}


