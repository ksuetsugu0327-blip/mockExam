<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkMClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 移行時のパスワードハッシュ化対応
        $WmClass = App\Models\WorkMClass::all();

        foreach ($WmClass as $cls) {
            $cls->password = Hash::make($cls->password);
            $cls->save();
        }
    }
}
