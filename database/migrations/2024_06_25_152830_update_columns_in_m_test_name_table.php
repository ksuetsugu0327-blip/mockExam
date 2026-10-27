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
    public function up()
    {
        Schema::table('m_test_name', function (Blueprint $table) {
            // Change column datatype from int unsigned to varchar(15)
            $table->string('test_name_cd', 15)->change();

            // Add display_seq column after test_name with comment '表示順' and default value 0
            $table->unsignedInteger('display_seq')->default(0)->after('test_name')->comment('表示順');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_test_name', function (Blueprint $table) {
            // Revert the test_name_cd column back to int unsigned
            $table->unsignedInteger('test_name_cd')->change();

            // Drop the display_seq column
            $table->dropColumn('display_seq');
        });
    }
};
