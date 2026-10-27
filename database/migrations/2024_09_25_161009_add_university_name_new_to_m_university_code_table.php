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
        Schema::table('m_university_code', function (Blueprint $table) {
            // Add the new column 'university_name_new' with VARCHAR(50) and default value of an empty string
            $table->string('university_name_new', 50)->default('')->after('university_name')->comment('大学名（新字）');
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
            // Rollback: remove the column 'university_name_new'
            $table->dropColumn('university_name_new');
        });
    }
};
