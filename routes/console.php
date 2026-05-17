<?php

use App\Console\Commands\PruneExpiredTokens;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(PruneExpiredTokens::class)
    ->hourly()
    ->description('Prune expired Sanctum personal access tokens');
