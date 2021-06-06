<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;
    use RefreshDatabase;

    use CreatesApplication;

    protected function setUp() : void
    {
        parent::setUp();

        $this->artisan('migrate',['-vvv' => true]);
        $this->artisan('passport:install',['-vvv' => true]);
        $this->artisan('db:seed',['-vvv' => true]);
    }

    /**
     * 전체 테이블 리스트.
     * 
     * @return array
     */
    public static function getTestTotalTablesList() : array
    {
        return DB::select("SELECT name FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' UNION ALL SELECT name FROM sqlite_temp_master WHERE type IN ('table', 'view') ORDER BY 1");
    }

    /**
     * 전체 테이블 리스트.
     */
    public static function printTotalTableList() : void
    {
        echo PHP_EOL.PHP_EOL;
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' UNION ALL SELECT name FROM sqlite_temp_master WHERE type IN ('table', 'view') ORDER BY 1");

        foreach($tables as $table)
        {
            echo "table-name: ".$table->name.PHP_EOL;
            echo "(".PHP_EOL;
            foreach(DB::getSchemaBuilder()->getColumnListing($table->name) as $columnName) {
                echo "\t".$columnName.PHP_EOL;
            }
            echo ")".PHP_EOL.PHP_EOL;
        }
        echo PHP_EOL;
    }

    /**
     * 해당 테이블 컬럼 리스트.
     * @param string $tableName
     * @return array
     */
    public static function getTableColumnList(string $tableName = "") : array
    {
        return DB::getSchemaBuilder()->getColumnListing($tableName);
    }

    /**
     * Request Header.
     * @return string[]
     */
    public static function getTestApiHeaders() : array
    {
        return [
            'Request-Client-Type' => 'S01010',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => ''
        ];
    }

    /**
     * 관리자 테스트용 토큰 포함 해더.
     * @return string[]
     */
    protected function getTestAccessTokenHeader() : array
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

    /**
     * 일반 로그인 사용자 테스트용 헤더.
     * @return string[]
     */
    protected function getTestGuestAccessTokenHeader() : array
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

    /**
     * 기본 에러 Response.
     * @return \string[][]
     */
    public static function getDefaultErrorJsonType() : array
    {
        return [
            'error' => [
                'error_message'
            ]
        ];
    }

    /**
     * Default 성공 Response.
     * @return string[]
     */
    public static function getDefaultSuccessJsonType() : array
    {
        return [
            "message" ,
            "result"
        ];
    }

    /**
     * 기본 성공 Response.
     * @return array
     */
    public static function getSuccessJsonType() : array
    {
        return [
            "message" => __('default.server.success')
        ];
    }
}
