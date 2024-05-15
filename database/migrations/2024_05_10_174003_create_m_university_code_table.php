<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMUniversityCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_university_code', function (Blueprint $table) {
            // Define columns with comments
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('university_code_head_id')->comment('大学コードヘッダーID');
            $table->string('university_cd', 4)->comment('大学コード');
            $table->unsignedTinyInteger('prefecture_cd')->comment('都道府県コード');
            $table->string('install_div', 1)->comment('設置区分');
            $table->string('dept_line_cd', 1)->comment('学部系統コード');
            $table->string('university_name_hkn', 100)->comment('大学名（半角カナ）');
            $table->string('university_name', 50)->comment('大学名');
            $table->string('department_cd', 4)->comment('学部コード');
            $table->integer('department_seq')->default(0)->comment('学部表示順');
            $table->string('department_name', 50)->comment('学部名');
            $table->string('subject_cd', 4)->nullable()->comment('学科コード');
            $table->integer('subject_seq')->nullable()->comment('学科表示順');
            $table->string('subject_name', 80)->nullable()->comment('学科名');
            $table->string('search_subject_name', 80)->nullable()->comment('検索用学科名');
            $table->unsignedTinyInteger('common_test_use')->default(0)->comment('共通テスト利用');
            $table->unsignedDecimal('deviation_value', 4, 1)->default(0)->comment('偏差値');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('作成日時');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新日時');

            // Define any foreign key constraints if necessary
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_university_code');
    }
}
