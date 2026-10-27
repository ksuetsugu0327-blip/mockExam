<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkMClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 移行時のパスワードハッシュ化対応
        $WmClass = App\Models\WorkMClass::all();

        foreach ($WmClass as $cls) {
            $cls->password = Hash::make($cls->password);
            $cls->save();
        }
    }
}
