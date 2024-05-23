<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_test_info')->insert([
            [
                'oem_sch_code' => 1001,
                'fiscal_year' => 2023,
                'grade' => 1,
                'test_name_cd' => 101,
                'university_code_head_id' => 1,
                'highschool_cls' => 'Science Class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'oem_sch_code' => 1002,
                'fiscal_year' => 2023,
                'grade' => 2,
                'test_name_cd' => 102,
                'university_code_head_id' => 2,
                'highschool_cls' => 'Arts Class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'oem_sch_code' => 1003,
                'fiscal_year' => 2023,
                'grade' => 3,
                'test_name_cd' => 103,
                'university_code_head_id' => 3,
                'highschool_cls' => 'Commerce Class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
