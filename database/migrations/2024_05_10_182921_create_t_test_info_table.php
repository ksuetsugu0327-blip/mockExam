<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTestInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_test_info', function (Blueprint $table) {
            // Define columns with comments
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('oem_sch_code')->comment('OEM塾コード');
            $table->unsignedSmallInteger('fiscal_year')->comment('年度');
            $table->unsignedTinyInteger('grade')->comment('学年区分');
            $table->unsignedInteger('test_name_cd')->nullable()->comment('テスト名コード');
            $table->unsignedBigInteger('university_code_head_id')->nullable()->comment('大学コードヘッダーID');
            $table->string('highschool_cls', 1000)->nullable()->comment('備考');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('作成日時');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新日時');

            // Add foreign key constraints if necessary (e.g., `university_code_head_id` might reference another table)
            // $table->foreign('university_code_head_id')->references('id')->on('another_table_name')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_test_info');
    }
}

