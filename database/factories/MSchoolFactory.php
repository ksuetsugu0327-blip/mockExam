<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(App\Models\MSchool::class, function (Faker $faker) {
    return [
        'sch_code' => $sch_code = $faker->unique()->numberBetween(1, 999),
        'name' => '塾名'.$sch_code,
        'cntconvert_kbn' => $faker->randomElement([' ', 'C', 'D', 'E', 'G', 'H', 'K', 'M', 'S', 'T']),
        'cnttest_kbn' => $faker->randomElement([1, 2, 3]),
        'testname' => 'テスト試験名'.$sch_code,
        'password' => Hash::make('password'),
        'admin_kbn' => '0'];
});
