<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->char('id', 15)->primary();
            $table->char('user_tarea_id', 12)->foreignId();
            $table->timestampsTz();

            $table->foreign('user_tarea_id')->references('id')->on('users_tareas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
        
    }
};
