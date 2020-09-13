<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function setUp() : void
    {
        parent::setUp();

        // $this->artisan('migrate',['-vvv' => true]);
        $this->artisan('passport:install',['-vvv' => true]);
        $this->artisan('db:seed',['-vvv' => true]);
    }

    protected function userCreate()
    {
        return factory('App\User')->create();
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
}
