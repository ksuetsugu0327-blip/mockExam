<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMTestcount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_testcount', function (Blueprint $table) {
            // カラムの追加を行う
            $table->string('printgrade1', 16)->nullable(); // 帳票印字用回数文字列(1年生用)
            $table->string('printgrade2', 16)->nullable(); // 帳票印字用回数文字列(2年生用)
            $table->string('printgrade3', 16)->nullable(); // 帳票印字用回数文字列(3年生用)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_testcount', function (Blueprint $table) {
            // 変更したい内容の処理を書く
        });
    }
}
