<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MSchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sch_code' => $sch_code = $this->faker->unique()->numberBetween(1, 999),
            'name' => '塾名'.$sch_code,
            'cntconvert_kbn' => $this->faker->randomElement([' ', 'C', 'D', 'E', 'G', 'H', 'K', 'M', 'S', 'T']),
            'cnttest_kbn' => $this->faker->randomElement([1, 2, 3]),
            'testname' => 'テスト試験名'.$sch_code,
            'password' => Hash::make('password'),
            'admin_kbn' => '0'];
    }
}
