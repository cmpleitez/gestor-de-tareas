<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->char('id', 12)->primary();
            $table->foreignId('tarea_id');
            $table->foreignId('oficina_id');
            $table->foreignId('user_id_origen');
            $table->foreignId('user_id_destino');
            $table->string('observacion')->nullable();
            $table->timestampsTz();
            
            $table->foreign('tarea_id')->references('id')->on('tareas');
            $table->foreign('oficina_id')->references('id')->on('oficinas');
            $table->foreign('user_id_origen')->references('id')->on('users');
            $table->foreign('user_id_destino')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
