<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMTestcount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_testcount', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('year')->nullable(false); // 年度
            $table->unsignedTinyInteger('cnttest')->nullable(false); // テスト回数
            $table->unsignedInteger('sch_code')->nullable(false); // 塾コード
            $table->unsignedInteger('test_day')->nullable(); // 学年
            $table->timestamps();
            // 削除も「物理削除」のため、削除フラグのカラムなし
            // プライマリキー設定
            $table->unique(['year', 'cnttest', 'sch_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_testcount');
    }
}
