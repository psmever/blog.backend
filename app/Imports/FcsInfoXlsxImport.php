<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\VilageFcstinfoMaster;
use App\Models\VilageFcstinfo;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithProgressBar;


class FcsInfoXlsxImport implements ToModel, WithProgressBar
{
    use Importable;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    /**
    * @param array $row
    */
    public function model(array $row)
    {
        $id = VilageFcstinfoMaster::latest()->first()->id;

        if($row[0] === '구분') {
            return null;
        }

        return new VilageFcstinfo([
            'version_id' => $id,
            'gubun' => trim($row[0]),
            'area_code' => trim($row[1]),
            'step1' => trim($row[2]),
            'step2' => trim($row[3]),
            'step3' => trim($row[4]),
            'grid_x' => trim($row[5]),
            'grid_y' => trim($row[6]),
            'longitude_hour' => trim($row[7]),
            'longitude_minute' => trim($row[8]),
            'longitude_second' => trim($row[9]),
            'latitude_hour' => trim($row[10]),
            'latitude_minute' => trim($row[11]),
            'latitude_second' => trim($row[12]),
            'longitude' => trim($row[13]),
            'latitude' => trim($row[14]),
            'update_time' => trim($row[15]),
        ]);
    }
}
