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
        Schema::create('m_directory', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('year')->nullable(false);       // 年度
            $table->unsignedTinyInteger('cnttest')->nullable(false);     // 試験回数
            $table->string('grade', 1)->nullable(false);                 // 学年
            $table->string('path_kbn', 1)->nullable(false);              // パス区分
            $table->string('category_kbn', 3)->nullable(false);          // 識別子
            $table->unsignedInteger('sch_code')->nullable();             // 塾コード
            $table->unsignedsmallInteger('cls_code')->nullable();              // 教室
            $table->string('psn_code', 11)->nullable();                  // 学籍番号(個人コード)
            $table->string('fullpath', 1023)->nullable();                // ファイルフルパス
            $table->timestamps();
            // 主キー
            // $table->unique(['cnttest','grade','path_kbn','category_kbn','process_kbn','sch_code','cls_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_directory');
    }
};
