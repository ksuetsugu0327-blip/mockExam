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
        Schema::create('m_school', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('sch_code')->nullable(false); // 塾コード
            $table->string('name', 40)->nullable(); // 名称
            $table->string('cntconvert_kbn', 1)->nullable(); // 管理No.6
            $table->integer('cnttest_kbn')->default(0)->nullable(); // テスト回数表示区分
            $table->string('testname', 40)->nullable(); // テスト名
            $table->string('password', 256)->nullable(false); // 塾アカウントパスワード
            $table->string('admin_kbn', 1)->nullable(); // 管理ユーザー区分
            $table->timestamps();

            // 削除も「物理削除」のため、削除フラグのカラムなし
            // プライマリキー設定
            $table->unique('sch_code');
        });
        // 定義追加
        // 塾コード（桁数指定）
        DB::statement('ALTER TABLE m_school CHANGE COLUMN sch_code sch_code BIGINT(10)');
        // テスト回収表示区分（桁数指定）
        DB::statement('ALTER TABLE m_school CHANGE COLUMN cnttest_kbn cnttest_kbn INTEGER(2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_school');
    }
};
