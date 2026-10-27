<?php

namespace Database\Factories;

use App\Models\MSchool;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sch_code = MSchool::All()->random()->sch_code;

        return [
            'sch_code' => $sch_code,
            'cls_code' => $cls_code = $this->faker->unique()->numberBetween(1, 9999),
            'name' => '教室名：'.$cls_code,
            'password' => Hash::make('password')];
    }
}
