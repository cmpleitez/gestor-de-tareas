<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_tarea', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id');
            $table->foreignId('tarea_id');
            $table->timestampsTz();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('tarea_id')->references('id')->on('tareas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_tareas');
    }
};
