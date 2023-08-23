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
        Schema::create('gurus', function (Blueprint $table) {
            $table->id('id_ust');
            $table->unsignedBigInteger('id_user');
            $table->string('nama');
            $table->string('gambar')->nullable();
            $table->string('role')->default('ust_pondok');
            $table->string('email')->unique(); // Pastikan email bersifat unik
            $table->string('password');
            $table->date('tgl_lahir'); // Menggunakan tipe data date untuk tanggal lahir
            $table->timestamps();

            $table->foreign('id_user')->references('id_masjid')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
