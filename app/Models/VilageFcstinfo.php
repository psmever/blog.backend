<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VilageFcstinfo extends Model
{
    use HasFactory;

    protected $table = "vilage_fcstinfo";

    protected $fillable = ['id', 'version_id', 'gubun', 'area_code', 'step1', 'step2', 'step3', 'grid_x', 'grid_y', 'longitude_hour', 'longitude_minute', 'longitude_second', 'latitude_hour', 'latitude_minute', 'latitude_second', 'longitude', 'latitude', 'update_time', 'active'];

}
