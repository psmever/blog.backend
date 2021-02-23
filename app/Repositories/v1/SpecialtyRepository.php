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

    // 기상청 Excel 데이터를 기본으로
    // 날짜 및 시간을 기준으로 날씨 에보 조회.
    public function getTopWeatherData($params)
    {
        return VilageFcstinfoMaster::where('active', 'Y')->orderBy('version', 'DESC')->first()->with(['weathers' => function($query) use ($params) {
                $query->where([['area_code', $params['area_code']], ['fcstDate', ">=", $params['fcstDate']], ['fcstTime', ">=", $params['fcstTime']]]);
                $query->limit(1)->orderBy('fcstDate', 'DESC');
            }, 'weathers.vilage'])->first();
    }

    // 날짜를 기준으로 조회.
    public function getTopWeatherDataSub($params)
    {
        return VilageFcstinfoMaster::where('active', 'Y')->orderBy('version', 'DESC')->first()->with(['weathers' => function($query) use ($params) {
                $query->where([['area_code', $params['area_code']], ['fcstDate', ">=", $params['fcstDate']]]);
                $query->limit(1)->orderBy('fcstDate', 'DESC');
            }, 'weathers.vilage'])->first();
    }
}
