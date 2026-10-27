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
        Schema::create('m_oem_school', function (Blueprint $table) {
            // Define columns with comments
            $table->unsignedBigInteger('oem_sch_code')->primary()->comment('OEM塾コード');
            $table->string('oem_sch_name', 40)->comment('塾名');
            $table->unsignedInteger('display_seq')->comment('表示順');
            $table->string('remarks', 100)->nullable()->comment('備考');
            $table->unsignedTinyInteger('invalid_flag')->default(0)->comment('有効無効フラグ');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('作成日時');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_oem_school');
    }
};
