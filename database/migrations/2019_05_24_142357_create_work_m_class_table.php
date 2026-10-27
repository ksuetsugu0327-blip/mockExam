<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkMClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_m_class', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('sch_code')->nullable(false); // 塾コード
            $table->smallInteger('cls_code')->nullable(false); // 教室コード
            $table->string('name', 20)->nullable; // 教室名
            $table->string('password', 256)->nullable(false); // 教室アカウントパスワード
            $table->timestamps();

            // プライマリキー設定
            $table->unique(['sch_code', 'cls_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_m_class');
    }
}
