<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CovidState extends Model
{
    use HasFactory;

    protected $table = "covid_state";

    protected $fillable = ['id', 'gubun_id', 'seq', 'createdt', 'deathcnt', 'incdec', 'isolclearcnt', 'qurrate', 'stdday', 'updatedt', 'defcnt', 'isolingcnt', 'overflowcnt', 'localocccnt'];
}
