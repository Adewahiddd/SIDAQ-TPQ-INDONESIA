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
        Schema::create('categori_kegiatans', function (Blueprint $table) {
            $table->id('id_kegiatan');
            $table->unsignedBigInteger('id_admin');
            $table->string('kegiatan');
            $table->timestamps();

            $table->foreign('id_admin')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categori_kegiatans');
    }
};
