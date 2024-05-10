<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMUniversityCodeNameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_university_code_name', function (Blueprint $table) {
            // Define columns with comments
            $table->unsignedInteger('university_code_name_cd')->primary()->comment('大学コードデータ名コード');
            $table->string('university_code_name', 60)->comment('大学コードデータ名');
            $table->string('remarks', 100)->nullable()->comment('備考');
            $table->unsignedTinyInteger('invalid_flag')->default(0)->comment('有効無効フラグ');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('作成日時');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_university_code_name');
    }
}
