<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarea_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id');
            $table->foreignId('user_id');
            $table->timestamps();

            $table->foreign('tarea_id')->references('id')->on('tareas');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarea_user');
    }
};
