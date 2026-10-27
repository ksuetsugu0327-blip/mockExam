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
        Schema::table('t_test_info', function (Blueprint $table) {
            $table->renameColumn('highschool_cls', 'remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_test_info', function (Blueprint $table) {
            $table->renameColumn('remarks', 'highschool_cls');
        });
    }
};
