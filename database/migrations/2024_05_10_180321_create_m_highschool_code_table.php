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
    public function up(): void
    {
        Schema::create('m_highschool_code', function (Blueprint $table) {
            // Define columns with comments
            $table->bigIncrements('id')->comment('ID');
            $table->string('highschool_code', 6)->comment('高等学校等コード');
            $table->string('highschool_name_hkn', 50)->comment('高等学校等名（半角カナ）');
            $table->string('zip_num', 8)->nullable()->comment('郵便番号');
            $table->string('tel_num', 20)->nullable()->comment('電話番号');
            $table->string('install_cd', 1)->comment('設置者区分');
            $table->string('class_cd', 1)->comment('高校種別コード');
            $table->string('highschool_name', 30)->comment('高等学校等名（全角）');
            $table->string('address', 40)->nullable()->comment('所在地（全角）');
            $table->unsignedTinyInteger('prefecture_cd')->comment('都道府県コード');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('作成日時');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('m_highschool_code');
    }
};
