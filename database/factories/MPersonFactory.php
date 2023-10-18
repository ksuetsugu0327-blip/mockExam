<?php

use Faker\Generator as Faker;
use App\Models\MClass as MClass;
use Illuminate\Support\Facades\Hash as Hash;
use Illuminate\Support\Facades\Crypt as Crypt;

$factory->define(App\Models\MPerson::class, function (Faker $faker) {
    $class = MClass::All()->random();
    
    return [
        'sch_code'=>$class->sch_code,
        'psn_code'=>$psn_code=$faker->unique()->numberBetween(1, 9999),
        'cls_code'=>$class->cls_code,
            'name'=>$faker->name(),
        'birthlow4'=> sprintf('%02d',$faker->numberBetween(4,7)).sprintf('%02d',$faker->month()),
        'postallow4'=>sprintf('%04d',$faker->numberBetween(1,9999)),
        'tellow4'=>sprintf('%04d',$faker->numberBetween(1,9999)),
        'password'=>Hash::make('password'),
        'temppassword'=>Crypt::encrypt('password')
    ];
});
