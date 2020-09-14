<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

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

    protected function getTestAccessTokenHeader()
    {
        $user = factory('App\User')->create();
        $response = $this->withHeaders($this->getTestApiHeaders())->postjson('/api/v1/auth/login', [
            "email" => $user->email,
            "password" => 'password'
        ]);
        return [
            'Request-Client-Type' => 'S01010',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer'.$response['access_token']
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
