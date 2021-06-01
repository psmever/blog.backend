<?php


namespace App\Supports\Facades;

use Illuminate\Support\Facades\Facade;

class GuitarClass extends Facade
{
    /**
     * @param bool $strtotime
     * @return string
     */
    public static function convertTimeToString(bool $strtotime)  : string
    {
    }

    /**
     * @return string
     */
    public static function randomNumberUUID() : string
    {
    }

    /**
     * @param string $text
     * @return string
     */
    public static function convertSlugString(string $text) : string
    {
    }

    /**
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return 'guitarclass';
    }
}
