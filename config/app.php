<?php

use Illuminate\Support\Facades\Facade;

return [

    'timezone' => 'Asia/Tokyo',

    'aliases' => Facade::defaultAliases()->merge([
        'Debugbar' => Barryvdh\Debugbar\Facade::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
