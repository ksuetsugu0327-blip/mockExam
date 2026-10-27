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
        // Create the 'm_university_code_head' table
        Schema::create('m_university_code_head', function (Blueprint $table) {
            // Define columns
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('university_code_name_cd')->comment('大学コードデータ名コード');
            $table->unsignedSmallInteger('fiscal_year')->comment('年度');
            $table->unsignedTinyInteger('dept_subject_div')->default(0)->comment('学部/学科判定区分');
            $table->unsignedSmallInteger('updated_num')->default(0)->comment('更新回数');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('作成日時');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'm_university_code_head' table
        Schema::dropIfExists('m_university_code_head');
    }
};
