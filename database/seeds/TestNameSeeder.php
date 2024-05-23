<?php

use Illuminate\Database\Seeder;

class TestNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_test_name')->insert([
            [
                'test_name_cd' => 101,
                'test_name' => 'テスト1',
                'remarks' => '特にない',
                'invalid_flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'test_name_cd' => 102,
                'test_name' => 'テスト2',
                'remarks' => '特にない',
                'invalid_flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'test_name_cd' => 103,
                'test_name' => 'テスト3',
                'remarks' => '特にない',
                'invalid_flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
