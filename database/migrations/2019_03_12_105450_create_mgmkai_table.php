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
        Schema::create('MGMKAI', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('KAIA001')->default(0)->nullable(); // テスト年度
            $table->string('KAIA010', 1)->default(' ')->nullable(); // 回数コンバート区分
            $table->unsignedTinyInteger('KAIA020')->default(0)->nullable(false); // 学年
            $table->string('KAIA030_1', 1)->nullable(); // （１）受験区分／テスト種別／ﾒｯｾｰｼﾞ種別
            $table->string('KAIA030_2', 1)->nullable();
            $table->string('KAIA030_3', 1)->nullable();
            $table->string('KAIA030_4', 1)->nullable();
            $table->string('KAIA030_5', 1)->nullable();
            $table->string('KAIA030_6', 1)->nullable();
            $table->string('KAIA030_7', 1)->nullable();
            $table->string('KAIA030_8', 1)->nullable();
            $table->string('KAIA030_9', 1)->nullable();
            $table->string('KAIA030_10', 1)->nullable();
            $table->string('KAIA030_11', 1)->nullable();
            $table->string('KAIA030_12', 1)->nullable();
            $table->string('KAIA030_13', 1)->nullable();
            $table->string('KAIA030_14', 1)->nullable();
            $table->string('KAIA030_15', 1)->nullable();
            $table->string('KAIA030_16', 1)->nullable();
            $table->string('KAIA030_17', 1)->nullable();
            $table->string('KAIA030_18', 1)->nullable();
            $table->string('KAIA030_19', 1)->nullable();
            $table->string('KAIA030_20', 1)->nullable();
            $table->string('KAIA030_21', 1)->nullable();
            $table->string('KAIA030_22', 1)->nullable();
            $table->string('KAIA030_23', 1)->nullable();
            $table->string('KAIA030_24', 1)->nullable();
            $table->string('KAIA030_25', 1)->nullable();
            $table->string('KAIA030_26', 1)->nullable();
            $table->string('KAIA030_27', 1)->nullable();
            $table->string('KAIA030_28', 1)->nullable();
            $table->string('KAIA030_29', 1)->nullable();
            $table->string('KAIA030_30', 1)->nullable();
            $table->string('KAIA030_31', 1)->nullable();
            $table->string('KAIA030_32', 1)->nullable();
            $table->string('KAIA030_33', 1)->nullable();
            $table->string('KAIA030_34', 1)->nullable();
            $table->string('KAIA030_35', 1)->nullable();
            $table->string('KAIA030_36', 1)->nullable();
            $table->string('KAIA030_37', 1)->nullable();
            $table->string('KAIA030_38', 1)->nullable();
            $table->string('KAIA030_39', 1)->nullable();
            $table->string('KAIA030_40', 1)->nullable();
            $table->string('KAIA030_41', 1)->nullable();
            $table->string('KAIA030_42', 1)->nullable();
            $table->string('KAIA030_43', 1)->nullable();
            $table->string('KAIA030_44', 1)->nullable();
            $table->string('KAIA030_45', 1)->nullable();
            $table->string('KAIA030_46', 1)->nullable();
            $table->string('KAIA030_47', 1)->nullable();
            $table->string('KAIA030_48', 1)->nullable();
            $table->string('KAIA030_49', 1)->nullable();
            $table->string('KAIA030_50', 1)->nullable();
            $table->string('KAIA030_51', 1)->nullable();
            $table->string('KAIA030_52', 1)->nullable();
            $table->string('KAIA030_53', 1)->nullable();
            $table->string('KAIA030_54', 1)->nullable();
            $table->string('KAIA030_55', 1)->nullable();
            $table->string('KAIA030_56', 1)->nullable();
            $table->string('KAIA030_57', 1)->nullable();
            $table->string('KAIA030_58', 1)->nullable();
            $table->string('KAIA030_59', 1)->nullable();
            $table->string('KAIA030_60', 1)->nullable();
            $table->string('KAIA030_61', 1)->nullable();
            $table->string('KAIA030_62', 1)->nullable();
            $table->string('KAIA030_63', 1)->nullable();
            $table->string('KAIA030_64', 1)->nullable();
            $table->string('KAIA030_65', 1)->nullable();
            $table->string('KAIA030_66', 1)->nullable();
            $table->string('KAIA030_67', 1)->nullable();
            $table->string('KAIA030_68', 1)->nullable();
            $table->string('KAIA030_69', 1)->nullable();
            $table->string('KAIA030_70', 1)->nullable();
            $table->string('KAIA030_71', 1)->nullable();
            $table->string('KAIA030_72', 1)->nullable();
            $table->string('KAIA030_73', 1)->nullable();
            $table->string('KAIA030_74', 1)->nullable();
            $table->string('KAIA030_75', 1)->nullable();
            $table->string('KAIA030_76', 1)->nullable();
            $table->string('KAIA030_77', 1)->nullable();
            $table->string('KAIA030_78', 1)->nullable();
            $table->string('KAIA030_79', 1)->nullable();
            $table->string('KAIA030_80', 1)->nullable();
            $table->string('KAIA030_81', 1)->nullable();
            $table->string('KAIA030_82', 1)->nullable();
            $table->string('KAIA030_83', 1)->nullable();
            $table->string('KAIA030_84', 1)->nullable();
            $table->string('KAIA030_85', 1)->nullable();
            $table->string('KAIA030_86', 1)->nullable();
            $table->string('KAIA030_87', 1)->nullable();
            $table->string('KAIA030_88', 1)->nullable();
            $table->string('KAIA030_89', 1)->nullable();
            $table->string('KAIA030_90', 1)->nullable();
            $table->string('KAIA030_91', 1)->nullable();
            $table->string('KAIA030_92', 1)->nullable();
            $table->string('KAIA030_93', 1)->nullable();
            $table->string('KAIA030_94', 1)->nullable();
            $table->string('KAIA030_95', 1)->nullable();
            $table->string('KAIA030_96', 1)->nullable();
            $table->string('KAIA030_97', 1)->nullable();
            $table->string('KAIA030_98', 1)->nullable();
            $table->string('KAIA030_99', 1)->nullable(); // （９９）受験区分／テスト種別／ﾒｯｾｰｼﾞ種別
            // プライマリキー設定
            $table->unique(['KAIA001', 'KAIA010', 'KAIA020']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('MGMKAI');
    }
};
