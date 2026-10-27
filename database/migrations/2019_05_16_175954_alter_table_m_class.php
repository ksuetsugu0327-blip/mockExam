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
        // 変更したい内容の処理を書く
        Schema::table('m_class', function (Blueprint $table) {
            $table->string('name', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 変更したい内容の処理を書く
        Schema::table('m_class', function (Blueprint $table) {
            $table->string('name', 20)->nullable(false)->change();
        });
    }
};
