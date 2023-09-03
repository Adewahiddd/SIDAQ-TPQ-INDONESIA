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
        Schema::create('kemampuans', function (Blueprint $table) {
            $table->id('id_kemampuan');
            $table->unsignedBigInteger('id_ustadz')->nullable();
            $table->unsignedBigInteger('id_santri')->nullable();
            $table->integer('khidmat');
            $table->integer('leadership');
            $table->integer('enterpreneur');
            $table->integer('speaking');
            $table->integer('operation');
            $table->integer('mengajar');
            $table->integer('admiristation');
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
        Schema::dropIfExists('kemampuans');
    }
};
