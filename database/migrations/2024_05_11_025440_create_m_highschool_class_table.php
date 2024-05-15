<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMHighschoolClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_highschool_class', function (Blueprint $table) {
            // Define columns with comments
            $table->string('class_cd', 1)->comment('高校種別コード');
            $table->string('class_name', 25)->comment('高校種別名');
            $table->primary(['class_cd','class_name']);
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
        Schema::dropIfExists('m_highschool_class');
    }
}
