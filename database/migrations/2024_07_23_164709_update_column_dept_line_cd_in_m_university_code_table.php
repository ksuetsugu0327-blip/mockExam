<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_university_code', function (Blueprint $table) {
            // Rename the column
            $table->string('dept_line_cd', 1)->comment('学部系統コード1')->change();
            $table->renameColumn('dept_line_cd', 'dept_line_cd1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_university_code', function (Blueprint $table) {
            // Reverse the column name change
            $table->renameColumn('dept_line_cd1', 'dept_line_cd');
            $table->string('dept_line_cd', 1)->comment('学部系統コード')->change();
        });
    }
};
