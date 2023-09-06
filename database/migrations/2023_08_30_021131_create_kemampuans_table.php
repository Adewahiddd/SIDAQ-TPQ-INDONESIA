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
            $table->unsignedBigInteger('id_ustadz');
            $table->unsignedBigInteger('id_santri');
            $table->integer('khidmat')->nullable();
            $table->integer('leadership')->nullable();
            $table->integer('enterpreneur')->nullable();
            $table->integer('speaking')->nullable();
            $table->integer('operation')->nullable();
            $table->integer('mengajar')->nullable();
            $table->integer('admiristation')->nullable();
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
