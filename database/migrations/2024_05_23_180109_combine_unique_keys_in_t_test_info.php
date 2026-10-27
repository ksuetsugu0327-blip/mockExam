<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('t_test_info', function (Blueprint $table) {
            // Drop the individual unique constraints
            $table->dropUnique('oem_sch_code');
            $table->dropUnique('fiscal_year');
            $table->dropUnique('grade');
            $table->dropUnique('test_name_cd');

            // Add the composite unique constraint
            $table->unique(['oem_sch_code', 'fiscal_year', 'grade', 'test_name_cd'], 'composite_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('t_test_info', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('composite_unique');

            // Re-add the individual unique constraints
            $table->unique('oem_sch_code');
            $table->unique('fiscal_year');
            $table->unique('grade');
            $table->unique('test_name_cd');
        });
    }
};
