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
            $table->unsignedBigInteger('id_ust');
            $table->string('nama');
            $table->string('gambar')->nullable();
            $table->string('role')->default('santri_pondok');
            $table->string('email');
            $table->string('password');
            $table->string('tgl_lahir');
            $table->string('ustadz');
            $table->string('amanah');
            $table->integer('kedisiplinan');
            $table->string('hafalans');
            $table->string('mutqin');
            $table->string('fundraising');
            $table->integer('alpha');
            $table->integer('sakit');
            $table->integer('izin');
            $table->string('tahajjud');
            $table->string('odoj');
            $table->string('stw');
            $table->string('majelis');
            $table->integer('khidmat');
            $table->integer('leadership');
            $table->integer('enterpreneur');
            $table->integer('speaking');
            $table->integer('operation');
            $table->integer('mengajar');
            $table->integer('administation');
            $table->integer('hafalan');
            $table->timestamps();
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
