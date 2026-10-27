<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('ftp', function ($app, $config) {
            return new Filesystem(new SftpAdapter([
                'host' => $config['host'],
                'port' => $config['port'],
                'username' => $config['username'],
                'password' => $config['password'],
                'root' => $config['root'],
                'timeout' => $config['timeout'],
            ]));
        });
    }
}
