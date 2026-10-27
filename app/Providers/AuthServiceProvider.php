<?php

namespace App\Providers;

use App\Http\Auth\CustomEloquentUserProvider;
use App\Http\Auth\CustomSessionGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // カスタムガードの追加
        Auth::extend('customSession', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            $guard = new CustomSessionGuard($name, $provider, $app['session.store']);
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($this->app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($this->app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });

        // カスタムEloquentUserプロバイダの追加
        Auth::provider('customEloquent', function ($app, array $config) {
            return new CustomEloquentUserProvider($app['hash'], $config['model']);
        });

    }
}
