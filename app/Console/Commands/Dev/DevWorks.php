<?php

namespace App\Console\Commands\Dev;

use Illuminate\Console\Command;

use App\Models\Posts;
use App\Models\PostsTags;
use App\Models\PostsThumbs;
use App\Models\MediaFiles;
use Illuminate\Support\Facades\Schema;

class DevWorks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:works {works}
    {--r|reset : table reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Local Developer Works Command';

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

        if($works == 'posts') {
            $this->postsWorks();
        }

        echo PHP_EOL;
        return 0;
    }

    public function postsWorks() {

        if($this->option('reset')) {
            $this->info('Posts Data Reset');

            $bar = $this->output->createProgressBar(4);
            $bar->start();

            Schema::disableForeignKeyConstraints();
            Posts::truncate();$bar->advance();
            PostsTags::truncate();$bar->advance();
            PostsThumbs::truncate();$bar->advance();
            MediaFiles::truncate();$bar->advance();
            Schema::enableForeignKeyConstraints();

            $bar->finish();
        }
    }
}
