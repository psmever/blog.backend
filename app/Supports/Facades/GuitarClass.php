<?php


namespace App\Supports\Facades;

use Illuminate\Support\Facades\Facade;

class GuitarClass extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return 'guitarclass';
    }
}