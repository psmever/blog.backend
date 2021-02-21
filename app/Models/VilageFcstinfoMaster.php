<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VilageFcstinfoMaster extends Model
{
    use HasFactory;

    protected $table = "vilage_fcstinfo_master";

    protected $fillable = ['id', 'version'];

    public function vilage_fcstinfos()
    {
        return $this->hasMany(VilageFcstinfo::class, 'version_id', 'id');
    }

    public function vilage_fcstinfo()
    {
        return $this->hasOne(VilageFcstinfo::class, 'version_id', 'id');
    }

    public function weathers() {

        return $this->hasManyThrough(
            Weathers::class,
            VilageFcstinfo::class,
            'version_id', // Foreign key on products table...
            'area_code_id', // Foreign key on orders table...
            'id', // Local key on countries table...
            'id' // Local key on users table...
        );
    }
}
