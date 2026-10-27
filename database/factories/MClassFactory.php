<?php

use App\Models\MSchool;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(App\Models\MClass::class, function (Faker $faker) {
    $sch_code = MSchool::All()->random()->sch_code;

    return [
        'sch_code' => $sch_code,
        'cls_code' => $cls_code = $faker->unique()->numberBetween(1, 9999),
        'name' => '教室名：'.$cls_code,
        'password' => Hash::make('password')];
});
