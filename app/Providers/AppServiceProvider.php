<?php

namespace App\Providers;

use App\Repositories\CommonCodeRepositoryInterface;
use App\Repositories\Eloquent\EloquentCommonCodeRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CommonCodeRepositoryInterface::class,
            EloquentCommonCodeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
