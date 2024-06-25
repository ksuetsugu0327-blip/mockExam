<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsInMUniversityCodeNameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_university_code_name', function (Blueprint $table) {
            // Change column datatype from int unsigned to varchar(15)
            $table->string('university_code_name_cd', 15)->change();

            // Add display_seq column after university_code_name with comment '表示順'
            $table->unsignedInteger('display_seq')->default(0)->after('university_code_name')->comment('表示順');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_university_code_name', function (Blueprint $table) {
            // Revert the university_code_name column back to int unsigned
            $table->unsignedInteger('university_code_name_cd')->change();

            // Drop the display_seq column
            $table->dropColumn('display_seq');
        });
    }
}
