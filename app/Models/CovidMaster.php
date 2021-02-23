<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
