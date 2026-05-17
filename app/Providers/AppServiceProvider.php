<?php

namespace App\Providers;

use App\Repositories\CommonCodeRepositoryInterface;
use App\Repositories\Eloquent\EloquentCommonCodeRepository;
use App\Repositories\Eloquent\EloquentPersonalAccessTokenRepository;
use App\Repositories\Eloquent\EloquentPostImageRepository;
use App\Repositories\Eloquent\EloquentPostRepository;
use App\Repositories\Eloquent\EloquentPostStatusHistoryRepository;
use App\Repositories\Eloquent\EloquentTagRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\PersonalAccessTokenRepositoryInterface;
use App\Repositories\PostImageRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\PostStatusHistoryRepositoryInterface;
use App\Repositories\TagRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );
        $this->app->bind(
            PersonalAccessTokenRepositoryInterface::class,
            EloquentPersonalAccessTokenRepository::class
        );
        $this->app->bind(
            CommonCodeRepositoryInterface::class,
            EloquentCommonCodeRepository::class
        );
        $this->app->bind(
            PostRepositoryInterface::class,
            EloquentPostRepository::class
        );
        $this->app->bind(
            PostImageRepositoryInterface::class,
            EloquentPostImageRepository::class
        );
        $this->app->bind(
            PostStatusHistoryRepositoryInterface::class,
            EloquentPostStatusHistoryRepository::class
        );
        $this->app->bind(
            TagRepositoryInterface::class,
            EloquentTagRepository::class
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
