<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public static function getTestApiHeaders()
    {
        return [
            'Request-Client-Type' => 'S01010',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    public static function getDefaultErrorJsonType()
    {
        return [
            'error_message'
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
