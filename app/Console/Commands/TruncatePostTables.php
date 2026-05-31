<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncatePostTables extends Command
{
    private const TABLES = [
        'post_status_histories',
        'post_tag',
        'post_image_variants',
        'post_images',
        'posts',
        'tags',
    ];

    protected $signature = 'posts:truncate-local';

    protected $description = 'Truncate post-related tables in the local environment';

    public function handle(): int
    {
        if (! app()->isLocal()) {
            $this->error('This command can only be run in the local environment.');

            return self::FAILURE;
        }

        $tables = collect(self::TABLES)
            ->filter(fn (string $table) => Schema::hasTable($table))
            ->values()
            ->all();

        if ($tables === []) {
            $this->warn('No post-related tables were found.');

            return self::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->info(sprintf(
            'Truncated post-related tables: %s',
            implode(', ', $tables)
        ));

        return self::SUCCESS;
    }
}
