<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairLocalMigrationRegistry extends Command
{
    protected $signature = 'migrations:repair-local';

    protected $description = 'Repair the local migration registry when schema objects already exist';

    public function handle(): int
    {
        if (! app()->isLocal()) {
            $this->error('This command can only be run in the local environment.');

            return self::FAILURE;
        }

        $migrationTable = (string) config('database.migrations.table', 'migrations');

        if (! Schema::hasTable($migrationTable)) {
            $this->callSilent('migrate:install');
        }

        $repository = DB::table($migrationTable);
        $existing = $repository->pluck('migration')->all();
        $existingLookup = array_fill_keys($existing, true);
        $batch = max(1, (int) $repository->max('batch'));

        $repaired = [];

        foreach ($this->repairRules() as $migration => $resolver) {
            if (isset($existingLookup[$migration])) {
                continue;
            }

            if (! $resolver()) {
                continue;
            }

            $repository->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);

            $repaired[] = $migration;
        }

        if ($repaired === []) {
            $this->info('No local migration registry repairs were needed.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Repaired local migration registry: %s',
            implode(', ', $repaired)
        ));

        return self::SUCCESS;
    }

    /**
     * @return array<string, \Closure(): bool>
     */
    private function repairRules(): array
    {
        return [
            '0001_01_01_000000_create_users_table' => fn (): bool => $this->allTablesExist([
                'users',
                'password_reset_tokens',
                'sessions',
            ]),
            '0001_01_01_000001_create_cache_table' => fn (): bool => $this->allTablesExist([
                'cache',
                'cache_locks',
            ]),
            '0001_01_01_000002_create_jobs_table' => fn (): bool => $this->allTablesExist([
                'jobs',
                'job_batches',
                'failed_jobs',
            ]),
            '2025_10_11_114731_create_common_codes_table' => fn (): bool => Schema::hasTable('common_codes'),
            '2025_10_12_131114_create_personal_access_tokens_table' => fn (): bool => Schema::hasTable('personal_access_tokens'),
            '2026_02_08_000000_create_posts_table' => fn (): bool => Schema::hasTable('posts'),
            '2026_02_08_000001_create_tags_table' => fn (): bool => Schema::hasTable('tags'),
            '2026_02_08_000002_create_post_tag_table' => fn (): bool => Schema::hasTable('post_tag'),
            '2026_02_08_000003_update_posts_slug_unique_index' => fn (): bool => $this->hasUserScopedOrFinalPostSlugIndex(),
            '2026_02_08_000004_add_uuid_to_posts_table' => fn (): bool => Schema::hasColumn('posts', 'uuid'),
            '2026_02_08_000005_add_status_and_published_at_to_posts_table' => fn (): bool => Schema::hasColumn('posts', 'status')
                && Schema::hasColumn('posts', 'published_at'),
            '2026_02_08_000006_create_post_status_histories_table' => fn (): bool => Schema::hasTable('post_status_histories'),
            '2026_02_08_000007_create_post_images_table' => fn (): bool => Schema::hasTable('post_images'),
            '2026_02_08_000008_add_cover_image_id_to_posts_table' => fn (): bool => Schema::hasColumn('posts', 'cover_image_id'),
            '2026_02_08_000009_stage_post_images_by_post_uuid' => fn (): bool => Schema::hasColumn('post_images', 'post_uuid'),
            '2026_05_09_000000_add_view_count_and_global_slug_to_posts_table' => fn (): bool => Schema::hasColumn('posts', 'view_count')
                && Schema::hasIndex('posts', ['slug'], 'unique'),
        ];
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function allTablesExist(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function hasUserScopedOrFinalPostSlugIndex(): bool
    {
        if (! Schema::hasTable('posts')) {
            return false;
        }

        if (Schema::hasIndex('posts', ['user_id', 'slug'], 'unique')) {
            return true;
        }

        return Schema::hasColumn('posts', 'view_count')
            && Schema::hasIndex('posts', ['slug'], 'unique');
    }
}
