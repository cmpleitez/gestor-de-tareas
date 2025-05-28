<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('equipo_id');
            $table->timestampsTz();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('equipo_id')->references('id')->on('equipos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_equipos');
    }
};
