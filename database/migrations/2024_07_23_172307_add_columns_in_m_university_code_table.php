<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInMUniversityCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_university_code', function (Blueprint $table) {
            // Add new columns with indexes
            $table->string('dept_line_cd2',1)->nullable()->after('dept_line_cd1')->comment('学部系統コード2');
            $table->string('dept_line_cd3',1)->nullable()->after('dept_line_cd2')->comment('学部系統コード3');
            $table->string('dept_line_cd4',1)->nullable()->after('dept_line_cd3')->comment('学部系統コード4');
            $table->string('dept_line_cd5',1)->nullable()->after('dept_line_cd4')->comment('学部系統コード5');
            $table->string('dept_line_cd6',1)->nullable()->after('dept_line_cd5')->comment('学部系統コード6');
            $table->string('dept_line_cd7',1)->nullable()->after('dept_line_cd6')->comment('学部系統コード7');

            $table->index([
                'dept_line_cd1',
                'dept_line_cd2',
                'dept_line_cd3',
                'dept_line_cd4',
                'dept_line_cd5',
                'dept_line_cd6',
                'dept_line_cd7'
            ], 'composite_dept_line_cd_index');
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
            // Drop the composite index
            $table->dropIndex(['composite_dept_line_cd_index']);

            // Drop the new columns
            $table->dropColumn('dept_line_cd2');
            $table->dropColumn('dept_line_cd3');
            $table->dropColumn('dept_line_cd4');
            $table->dropColumn('dept_line_cd5');
            $table->dropColumn('dept_line_cd6');
            $table->dropColumn('dept_line_cd7');
        });
    }
}
