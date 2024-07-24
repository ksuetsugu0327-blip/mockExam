<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnDeptLineCdInMUniversityCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_university_code', function (Blueprint $table) {
            // Rename the column
            $table->string('dept_line_cd', 1)->comment('学部系統コード1')->change();
            $table->renameColumn('dept_line_cd', 'dept_line_cd1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_university_code', function (Blueprint $table) {
            // Reverse the column name change
            $table->renameColumn('dept_line_cd1', 'dept_line_cd');
            $table->string('dept_line_cd', 1)->comment('学部系統コード')->change();
        });
    }
}
