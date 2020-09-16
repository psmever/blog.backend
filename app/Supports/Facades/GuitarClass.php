<?php

namespace App\Supports\Facades;

use Illuminate\Support\Facades\Facade;

class GuitarClass extends Facade
{
    protected static function getFacadeAccessor() { return 'guitarclass'; }
}
