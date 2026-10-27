<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_person', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sch_code')->nullable(false);       // 塾コード
            $table->string('psn_code', 11)->nullable(false);             // 個人コード
            $table->unsignedSmallInteger('cls_code')->nullable(false);  // 教室コード
            $table->string('name', 20)->nullable();                     // 氏名（漢字）
            $table->string('birthlow4', 4)->nullable();                 // 生年月日（下4桁）
            $table->string('postallow4', 4)->nullable();                // 郵便番号（下4桁）
            $table->string('tellow4', 4)->nullable();                   // 電話番号（下4桁）
            $table->string('password', 256)->nullable();                // 本パスワード
            $table->string('temppassword', 256)->nullable();            // 仮パスワード
            $table->timestamps();

            // 主キー
            $table->unique(['sch_code', 'psn_code', 'cls_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_person');
    }
}
