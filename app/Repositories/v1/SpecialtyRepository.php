<?php

namespace App\Repositories\v1;


use App\Models\VilageFcstinfoMaster;
use App\Models\VilageFcstinfo;
use App\Models\Weathers;


class SpecialtyRepository implements SpecialtyRepositoryInterface
{

    protected $Weathers;
    protected $VilageFcstinfoMaster;
    protected $VilageFcstinfo;


    public function __construct(Weathers $weathers, VilageFcstinfoMaster $vilageFcstinfoMaster, VilageFcstinfo $vilageFcstinfo)
    {
        $this->Weathers = $weathers;
        $this->VilageFcstinfoMaster = $vilageFcstinfoMaster;
        $this->VilageFcstinfo = $vilageFcstinfo;
    }

    public function getAll() {}
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}

    public function getTopWeatherData()
    {

    }

}
