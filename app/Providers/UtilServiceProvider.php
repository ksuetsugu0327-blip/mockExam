<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UtilServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->bind(\App\Utils\PasswordUtil::class);
        $this->app->bind(\App\Utils\SessionUtil::class);
        $this->app->bind(\App\Utils\FileUtil::class);
        $this->app->bind(\App\Utils\RouteUtil::class);
    }
}
