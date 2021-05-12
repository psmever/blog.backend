<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

            return new \App\Repositories\GuitarClass();

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
