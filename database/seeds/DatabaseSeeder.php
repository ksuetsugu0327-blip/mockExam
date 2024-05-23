<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TestNameSeeder::class,
            TestInfoSeeder::class,
            UniversityCodeNameSeeder::class,
            UniversityCodeHeadSeeder::class,
            // Add other seeders here
        ]);
    }
}
