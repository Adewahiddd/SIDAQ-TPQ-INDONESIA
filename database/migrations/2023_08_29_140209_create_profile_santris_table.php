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
        Schema::create('profile_santris', function (Blueprint $table) {
        $table->id('id_profile');
        $table->unsignedBigInteger('id_user');
        $table->unsignedBigInteger('id_admin')->nullable();
        $table->unsignedBigInteger('id_ustadz')->nullable();
        $table->unsignedBigInteger('id_santri')->nullable();
        $table->string('gambar');
        $table->date('tgl_lahir')->nullable();
        $table->string('gender')->nullable();
        $table->string('angkatan')->nullable();
        $table->string('provinsi')->nullable();
        $table->string('kabupaten')->nullable();
        $table->string('alamat_masjid')->nullable();
        $table->boolean('verifikasi')->nullable();
        $table->timestamps();


        // Relasi dengan tabel users
        $table->foreign('id_admin')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_ustadz')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_santri')->references('id_user')->on('users')->onDelete('cascade');
        $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
    });



        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_santris');
    }
};
