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
        Schema::table('t_test_info', function (Blueprint $table) {
            // Add the new column
            $table->string('codebook_cd', 15)->nullable()->comment('コード表コード')->after('university_code_head_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_test_info', function (Blueprint $table) {
            // Drop the new column
            $table->dropColumn('codebook_cd');
        });
    }
};
