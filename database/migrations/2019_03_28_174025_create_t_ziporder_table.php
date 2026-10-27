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
        Schema::create('t_ziporder', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category_kbn', 3)->nullable(false);          // 発行区分
            $table->string('process_kbn', 1)->nullable(false);           // 処理状況区分
            $table->unsignedSmallInteger('year')->nullable(false);       // 年度
            $table->unsignedSmallInteger('cnttest')->nullable(false);    // 回数
            $table->unsignedTinyInteger('grade')->nullable(false);       // 学年
            $table->unsignedInteger('sch_code')->nullable(false);        // 塾コード
            $table->smallInteger('cls_code')->nullable();                // 教室コード
            $table->string('zipfilepath', 1023)->nullable();             // 圧縮ファイル格納先パス
            $table->string('zipfilepass', 256)->nullable();              // 圧縮ファイルパスワード
            $table->string('error_kbn', 1)->nullable();                  // エラー区分
            $table->timestamp('order_at')->nullable();                  // 発行指示日時
            $table->timestamp('zipfile_at')->nullable();                // 圧縮ファイル作成日時
            $table->timestamp('download_at')->nullable();               // ダウンロード日時
            $table->timestamps();

            // ユニークキー
            // $table->primary(['category_kbn','year','cnttest','grade','sch_code','cls_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_ziporder');
    }
};
