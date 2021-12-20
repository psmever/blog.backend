<?php

namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendDevData extends Command
{
    protected $prodTables = ['covid_master', 'covid_state', 'vilage_fcstinfo_master', 'vilage_fcstinfo', 'weathers', 'media_files', 'posts', 'posts_tags', 'posts_thumbs', 'section_posts'];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:send-schema-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '데이터베이스 개발 서버 디비로 전송.';

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
        DB::connection('dev_mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->truncateTableDevServer();

        $this->sendProdData();

        DB::connection('dev_mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');

        return 0;
    }

    public function truncateTableDevServer()
    {
        foreach ($this->prodTables as $table):
            DB::connection('dev_mysql')->table($table)->truncate();
        endforeach;
    }

    public function sendProdData()
    {
        foreach ($this->prodTables as $table):

            $items = DB::table($table)->get();

            foreach ($items as $item):
                DB::connection('dev_mysql')->table($table)->insert((array) $item);
            endforeach;

        endforeach;
    }
}
