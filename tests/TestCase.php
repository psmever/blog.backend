<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    // use DatabaseMigrations;
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->artisan('migrate',['-vvv' => true]);
        $this->artisan('passport:install',['-vvv' => true]);
        $this->artisan('db:seed',['-vvv' => true]);
    }

    public static function getTestTotalTablesList()
    {
        return DB::select("SELECT name FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' UNION ALL SELECT name FROM sqlite_temp_master WHERE type IN ('table', 'view') ORDER BY 1");
    }

    public static function printrTotalTableList()
    {
        echo PHP_EOL.PHP_EOL;
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' UNION ALL SELECT name FROM sqlite_temp_master WHERE type IN ('table', 'view') ORDER BY 1");

        foreach($tables as $table)
        {
            echo $table->name.PHP_EOL;
            print_r(DB::getSchemaBuilder()->getColumnListing($table->name));
        }
        echo PHP_EOL;
    }

    public static function getTableColumnList($tableName = "")
    {
        return DB::getSchemaBuilder()->getColumnListing($tableName);
    }


    public static function getTestApiHeaders()
    {
        return [
            'Request-Client-Type' => 'S01010',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => ''
        ];
    }

    protected function getTestAccessTokenHeader()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => \App\Models\User::where('user_level', 'S02900')->orderBy('id', 'ASC')->first()->email,
            "password" => 'password'
        ]);
        return [
            'Request-Client-Type' => 'S01010',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$response['access_token']
        ];
    }

    protected function getTestGuestAccessTokenHeader()
    {
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => \App\Models\User::where('user_level', 'S02010')->orderBy('id', 'ASC')->first()->email,
            "password" => 'password'
        ]);
        return [
            'Request-Client-Type' => 'S01010',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$response['access_token']
        ];
    }

    public static function getDefaultErrorJsonType()
    {
        return [
            'error' => [
                'error_message'
            ]
        ];
    }

    public static function getDefaultSuccessJsonType()
    {
        return [
            "message" ,
            "result"
        ];
    }

    public static function getSuccessJsonType()
    {
        return [
            "message" => __('default.server.success')
        ];
    }
}
