<?php

namespace App\Console\Commands\Dev;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use \App\Imports\FcsInfoXlsxImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

use App\Models\VilageFcstinfoMaster;
use App\Models\VilageFcstinfo;

class DevWeatherExcel extends Command
{

    private $xlsxFileName = "apis_data_go_kr_latitude_longitude.xlsx";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:weatherexcel {works} {version}
    {--r|reset : table reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '기상청 격자 데이터 업데이트 격자 데이터 엑셀 데이터 디비 입력. 기상청18_동네예보 조회서비스_오픈API활용가이드.zip https://www.data.go.kr/iim/api/selectAPIAcountView.do
    ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $works = $this->argument('works');

        if($works === 'new') {
            $this->newWorks();
        }

        echo PHP_EOL;
        return 0;
    }

    public function newWorks()
    {

        if(!Storage::disk('forlocal')->exists($this->xlsxFileName)) {
            echo "xlsx file not found";
            return 0;
        }

        $version = $this->argument('version') ? $this->argument('version') : \Carbon\Carbon::createFromFormat('Ymd');

        Schema::disableForeignKeyConstraints();
        VilageFcstinfoMaster::truncate();
        Schema::enableForeignKeyConstraints();

        Schema::disableForeignKeyConstraints();
        VilageFcstinfo::truncate();
        Schema::enableForeignKeyConstraints();

        VilageFcstinfoMaster::create([
            'version' => $version
        ]);

        $this->output->title('Starting import');

        $filePath = Storage::disk('forlocal')->getAdapter()->applyPathPrefix($this->xlsxFileName);
        (new FcsInfoXlsxImport)->withOutput($this->output)->import($filePath);
        $this->output->success('Import successful');
    }
}
