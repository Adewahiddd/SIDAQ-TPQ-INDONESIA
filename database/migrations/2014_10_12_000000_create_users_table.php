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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_masjid');
            $table->string('nama_masjid');
            $table->string('gambar');
            $table->string('role')->default('admin_pondok');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('alamat_masjid');
            $table->boolean('verifikasi')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
