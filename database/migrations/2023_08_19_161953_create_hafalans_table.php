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
        Schema::create('hafalans', function (Blueprint $table) {
            $table->id('id_hafalan');
            $table->unsignedBigInteger('id_santri');
            $table->integer('tanggal');
            $table->string('nama_santri');
            $table->string('surah');
            $table->integer('jumlah_ayat');
            $table->integer('nilai');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafalans');
    }
};
