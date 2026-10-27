<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversityCodeHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_university_code_head')->insert([
            [
                'university_code_name_cd' => 1,
                'fiscal_year' => 2023,
                'dept_subject_div' => 0,
                'updated_num' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'university_code_name_cd' => 2,
                'fiscal_year' => 2023,
                'dept_subject_div' => 1,
                'updated_num' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'university_code_name_cd' => 3,
                'fiscal_year' => 2024,
                'dept_subject_div' => 0,
                'updated_num' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
