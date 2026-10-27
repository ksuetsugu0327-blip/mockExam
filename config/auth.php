<?php

return [

    'guards' => [
        'web' => [
            'driver' => 'customSession',
            'provider' => 'users',
        ],

        'ADMIN' => [
            'driver' => 'customSession',
            'provider' => 'schools',
        ],

        'SCHOOL' => [
            'driver' => 'customSession',
            'provider' => 'schools',
        ],

        'CLASS' => [
            'driver' => 'customSession',
            'provider' => 'classes',
        ],

        'PERSON' => [
            'driver' => 'customSession',
            'provider' => 'persons',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    'providers' => [
        'schools' => [
            'driver' => 'eloquent',
            'model' => App\Models\MSchool::class,
        ],

        'classes' => [
            'driver' => 'eloquent',
            'model' => App\Models\MClass::class,
        ],

        'persons' => [
            'driver' => 'eloquent',
            'model' => App\Models\MPerson::class,
        ],
    ],

];
