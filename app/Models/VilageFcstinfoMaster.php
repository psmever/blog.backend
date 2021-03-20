<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VilageFcstinfoMaster
 *
 * @property int $id
 * @property string $version
 * @property string $active 사용 유무.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VilageFcstinfo|null $vilage_fcstinfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VilageFcstinfo[] $vilage_fcstinfos
 * @property-read int|null $vilage_fcstinfos_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Weathers[] $weathers
 * @property-read int|null $weathers_count
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereVersion($value)
 * @mixin \Eloquent
 */
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
