<?php

return [

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
            'serve' => true,
            'report' => false,
        ],

        'ftp' => [
            'driver' => 'ftp',
            'host' => env('FILE_SYSTEM_HOST'),
            'port' => env('FILE_SYSTEM_PORT'),
            'username' => env('FILE_SYSTEM_USERNAME'),
            'password' => env('FILE_SYSTEM_PASSWORD'),
            'root' => env('FILE_SYSTEM_ROOT'),
            'timeout' => env('FILE_SYSTEM_TIMEOUT'),
        ],

        'sftp' => [
            'driver' => 'sftp',
            'host' => env('FILE_SYSTEM_HOST'),
            'port' => 22,
            'username' => env('FILE_SYSTEM_USERNAME'),
            'password' => env('FILE_SYSTEM_PASSWORD'),
            'root' => env('FILE_SYSTEM_ROOT'),
            'timeout' => env('FILE_SYSTEM_TIMEOUT'),

            // SSH keyベースの認証の設定
            // 'privateKey' => '/path/to/privateKey',
            // 'password' => 'encryption-password',

            // FTP設定のオプション
            // 'port' => 22,
            // 'root' => '',
            // 'timeout' => 30,
        ],
    ],

];
