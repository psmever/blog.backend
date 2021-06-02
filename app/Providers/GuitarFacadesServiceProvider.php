<?php

namespace App\Providers;

use App\Repositories\GuitarClass;
use Illuminate\Support\ServiceProvider;


/**
 * Class GuitarFacadesServiceProvider
 * @package App\Providers
 */
class GuitarFacadesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('guitarclass', function() {

            return new GuitarClass();

        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
