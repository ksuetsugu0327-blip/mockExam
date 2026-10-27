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
        Schema::create('m_codebook', function (Blueprint $table) {
            $table->string('codebook_cd', 15)->primary(); // Primary key
            $table->string('codebook_name', 60);
            $table->string('codebook_file_name', 60);
            $table->string('remarks', 100)->nullable();
            $table->unsignedInteger('display_seq')->default(0);
            $table->unsignedTinyInteger('invalid_flag')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_codebook');
    }
};
