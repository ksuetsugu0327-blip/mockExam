<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversityCodeNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_university_code_name')->insert([
            [
                'university_code_name_cd' => 1,
                'university_code_name' => 'Tokyo University',
                'remarks' => 'Top university in Japan',
                'invalid_flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'university_code_name_cd' => 2,
                'university_code_name' => 'Kyoto University',
                'remarks' => 'Known for research',
                'invalid_flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'university_code_name_cd' => 3,
                'university_code_name' => 'Osaka University',
                'remarks' => 'Strong in science and technology',
                'invalid_flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
