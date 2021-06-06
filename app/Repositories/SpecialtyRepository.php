<?php

namespace App\Repositories;


use App\Models\VilageFcstinfoMaster;
use App\Models\VilageFcstinfo;
use App\Models\Weathers;
use App\Models\CovidMaster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SpecialtyRepository
 * @package App\Repositories
 */
class SpecialtyRepository implements SpecialtyRepositoryInterface
{

    /**
     * @var Weathers
     */
    protected Weathers $Weathers;

    /**
     * @var VilageFcstinfoMaster
     */
    protected VilageFcstinfoMaster $VilageFcstinfoMaster;

    /**
     * @var VilageFcstinfo
     */
    protected VilageFcstinfo $VilageFcstinfo;

    /**
     * SpecialtyRepository constructor.
     * @param Weathers $weathers
     * @param VilageFcstinfoMaster $vilageFcstinfoMaster
     * @param VilageFcstinfo $vilageFcstinfo
     */
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

    /**
     * 기상청 Excel 데이터를 기본으로
     * 날짜 및 시간을 기준으로 날씨 에보 조회.
     *
     * @param $params
     * @return VilageFcstinfoMaster|Builder|Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getTopWeatherData($params)
    {
        return (new VilageFcstinfoMaster)->where('active', 'Y')->orderBy('version', 'DESC')->first()->with(['weathers' => function($query) use ($params) {
                $query->where([['area_code', $params['area_code']], ['fcstDate', ">=", $params['fcstDate']], ['fcstTime', ">=", $params['fcstTime']]]);
                $query->limit(1)->orderBy('fcstDate', 'DESC');
            }, 'weathers.vilage'])->first();
    }

    /**
     * 날짜를 기준으로 조회.
     * @param $params
     * @return VilageFcstinfoMaster|Builder|Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getTopWeatherDataSub($params)
    {
        return (new VilageFcstinfoMaster)->where('active', 'Y')->orderBy('version', 'DESC')->first()->with(['weathers' => function($query) use ($params) {
                $query->where([['area_code', $params['area_code']], ['fcstDate', ">=", $params['fcstDate']]]);
                $query->limit(1)->orderBy('fcstDate', 'DESC');
            }, 'weathers.vilage'])->first();
    }

    /**
     * covid19 데이터 조회.
     *
     * @return Builder[]|Collection
     */
    public function getCovidState()
    {
        return CovidMaster::with(['covid_state'])->get();
    }

    /**
     * covid19 오늘 어제 데이터.
     * @return CovidMaster|Builder|Model|object|null
     */
    public function getCovidTodayTotal()
    {
        return CovidMaster::where('title', '=', 'total')->with(['covid_total'])->first();
    }
}
