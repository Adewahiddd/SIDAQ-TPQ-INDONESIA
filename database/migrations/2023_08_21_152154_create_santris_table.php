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
        Schema::create('santris', function (Blueprint $table) {
            $table->id('id_santri');
            $table->unsignedBigInteger('id_ust'); // Kolom baru untuk menghubungkan ke tabel gurus
            $table->string('nama');
            $table->string('gambar')->nullable();
            $table->string('role')->default('santri_pondok');
            $table->string('email')->unique(); // Pastikan email bersifat unik
            $table->string('password');
            $table->date('tgl_lahir'); // Menggunakan tipe data date untuk tanggal lahir
            $table->string('ustadz');
            $table->string('amanah');
            $table->integer('kedisiplinan');
            $table->string('hafalans');
            $table->string('mutqin');
            $table->string('fundraising');
            $table->string('image'); // Apakah ini kolom yang sama dengan 'gambar' di atas?
            $table->date('tanggal'); // Menggunakan tipe data date untuk tanggal
            $table->integer('alpha');
            $table->integer('sakit');
            $table->integer('izin');
            $table->string('tahajjud');
            $table->string('odoj');
            $table->string('stw');
            $table->string('majelis');
            $table->integer('khidmat');
            $table->integer('leadership');
            $table->integer('entrepreneur'); // Menggunakan ejaan yang benar
            $table->integer('speaking');
            $table->integer('operation');
            $table->integer('mengajar');
            $table->integer('administration'); // Menggunakan ejaan yang benar
            $table->integer('hafalan');
            $table->timestamps();

            $table->foreign('id_ust')->references('id_ust')->on('gurus')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
