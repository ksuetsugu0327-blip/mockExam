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
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Utils\PasswordUtil::class);
        $this->app->bind(\App\Utils\SessionUtil::class);
        $this->app->bind(\App\Utils\FileUtil::class);
        $this->app->bind(\App\Utils\RouteUtil::class);
    }
}
