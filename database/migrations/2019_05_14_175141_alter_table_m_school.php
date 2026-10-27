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
        // 変更したい内容の処理を書く
        Schema::table('m_school', function (Blueprint $table) {
            $table->string('psn_hide_flg', 1)
                ->after('admin_kbn')
                ->comment('生徒機能非表示フラグ 0=表示,1=非表示')
                ->nullable()
                ->default('0');
        });

        Schema::table('m_school', function (Blueprint $table) {
            $table->string('schname_hide_flg', 1)
                ->after('psn_hide_flg')
                ->comment('塾名非表示フラグ 0=表示,1=非表示')
                ->nullable()
                ->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 変更した内容を取り消す処理を書く
        Schema::table('m_school', function (Blueprint $table) {
            $table->dropColumn('psn_hide_flg');
        });

        Schema::table('m_school', function (Blueprint $table) {
            $table->dropColumn('schname_hide_flg');
        });
    }
};
