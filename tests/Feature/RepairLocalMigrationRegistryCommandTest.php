<?php

namespace Tests\Feature;

use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RepairLocalMigrationRegistryCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
    }

    public function test_repair_command_restores_missing_registry_rows_for_existing_schema(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        DB::table('migrations')
            ->whereIn('migration', [
                '0001_01_01_000001_create_cache_table',
                '2026_02_08_000006_create_post_status_histories_table',
            ])
            ->delete();

        $this->artisan('migrations:repair-local')
            ->expectsOutput(
                'Repaired local migration registry: 0001_01_01_000001_create_cache_table, 2026_02_08_000006_create_post_status_histories_table'
            )
            ->assertExitCode(0);

        $this->assertDatabaseHas('migrations', [
            'migration' => '0001_01_01_000001_create_cache_table',
        ]);
        $this->assertDatabaseHas('migrations', [
            'migration' => '2026_02_08_000006_create_post_status_histories_table',
        ]);
    }

    public function test_repair_command_reports_when_no_repairs_are_needed(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $this->artisan('migrations:repair-local')
            ->expectsOutput('No local migration registry repairs were needed.')
            ->assertExitCode(0);
    }

    public function test_repair_command_fails_outside_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'testing');

        $this->artisan('migrations:repair-local')
            ->expectsOutput('This command can only be run in the local environment.')
            ->assertExitCode(1);
    }
}
