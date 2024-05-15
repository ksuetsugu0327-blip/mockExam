<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMHighschoolInstallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_highschool_install', function (Blueprint $table) {
            // Define columns with comments
            $table->string('install_cd1', 1)->comment('設置者コード1');
            $table->string('install_cd2', 1)->comment('設置者コード2');
            $table->primary(['install_cd1', 'install_cd2']);
            $table->string('install_name1', 4)->comment('設置者区分名1');
            $table->string('install_name2', 8)->comment('設置者区分名2');
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
        Schema::dropIfExists('m_highschool_install');
    }
}
