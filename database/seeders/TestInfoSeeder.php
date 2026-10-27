<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('t_test_info')->insert([
            [
                'oem_sch_code' => 1001,
                'fiscal_year' => 2023,
                'grade' => 1,
                'test_name_cd' => 101,
                'university_code_head_id' => 1,
                'remarks' => 'Science Class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'oem_sch_code' => 1002,
                'fiscal_year' => 2023,
                'grade' => 2,
                'test_name_cd' => 102,
                'university_code_head_id' => 2,
                'remarks' => 'Arts Class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'oem_sch_code' => 1003,
                'fiscal_year' => 2023,
                'grade' => 3,
                'test_name_cd' => 103,
                'university_code_head_id' => 3,
                'remarks' => 'Commerce Class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
