<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnDeptLineCd1InMUniversityCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_university_code', function (Blueprint $table) {
            $table->string('dept_line_cd1', 1)->comment('学部系統コード1')->change();

            // Add index to existing column
            $table->index('dept_line_cd1');
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
            $table->string('dept_line_cd1', 1)->comment('学部系統コード')->change();

            // Drop the indexes from the columns
            $table->dropIndex(['dept_line_cd1']);
        });
    }
}
