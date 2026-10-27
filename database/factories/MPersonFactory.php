<?php

namespace Database\Factories;

use App\Models\MClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class MPersonFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $class = MClass::All()->random();

        return [
            'sch_code' => $class->sch_code,
            'psn_code' => $psn_code = $this->faker->unique()->numberBetween(1, 9999),
            'cls_code' => $class->cls_code,
            'name' => $this->faker->name(),
            'birthlow4' => sprintf('%02d', $this->faker->numberBetween(4, 7)).sprintf('%02d', $this->faker->month()),
            'postallow4' => sprintf('%04d', $this->faker->numberBetween(1, 9999)),
            'tellow4' => sprintf('%04d', $this->faker->numberBetween(1, 9999)),
            'password' => Hash::make('password'),
            'temppassword' => Crypt::encrypt('password'),
        ];
    }
}
