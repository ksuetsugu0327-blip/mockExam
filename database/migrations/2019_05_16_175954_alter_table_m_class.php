<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 変更したい内容の処理を書く
        Schema::table('m_class', function (Blueprint $table) {
            $table->string('name', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 変更したい内容の処理を書く
        Schema::table('m_class', function (Blueprint $table) {
            $table->string('name', 20)->nullable(false)->change();
        });
    }
}
