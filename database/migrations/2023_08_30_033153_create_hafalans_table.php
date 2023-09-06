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
            $table->unsignedBigInteger('id_ustadz');
            $table->unsignedBigInteger('id_santri');
            $table->date('tanggal');
            $table->string('surah');
            $table->integer('jumlah_ayat');
            $table->integer('nilai');
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
        Schema::dropIfExists('hafalans');
    }
};
