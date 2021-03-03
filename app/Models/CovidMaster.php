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

    // 현재 및 이전 데이터용.
    public function covid_total()
    {
        return $this->hasMany(CovidState::class, 'gubun_id' , 'id')->orderBy('id', 'desc')->take(2);
    }
}
