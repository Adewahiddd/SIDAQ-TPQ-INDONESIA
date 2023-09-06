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
        Schema::create('amal_sholehs', function (Blueprint $table) {
            $table->id('id_amal');
            $table->unsignedBigInteger('id_ustadz');
            $table->unsignedBigInteger('id_santri');
            $table->unsignedBigInteger('id_amanah');
            $table->string('hafalan')->nullable();
            $table->string('mutqin')->nullable();
            $table->string('gambar')->nullable();
            $table->string('fundraising')->nullable();
            $table->string('amanah')->nullable();
            $table->integer('kedisiplinan')->nullable();
            $table->timestamps();

            $table->foreign('id_ustadz')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_santri')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amal_sholehs');
    }
};
