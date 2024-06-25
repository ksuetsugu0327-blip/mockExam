<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameHighschoolClsToRemarksInTTestInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_test_info', function (Blueprint $table) {
            $table->renameColumn('highschool_cls', 'remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_test_info', function (Blueprint $table) {
            $table->renameColumn('remarks', 'highschool_cls');
        });
    }
}
