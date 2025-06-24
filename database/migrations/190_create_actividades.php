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
            $table->char('recepcion_id', 10)->foreignId();
            $table->foreignId('tarea_id');
            $table->foreignId('role_id');
            $table->foreignId('user_id_origen');
            $table->foreignId('user_id_destino');
            $table->string('observacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestampsTz();
            
            $table->foreign('recepcion_id')->references('id')->on('recepciones');
            $table->foreign('tarea_id')->references('id')->on('tareas');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('user_id_origen')->references('id')->on('users');
            $table->foreign('user_id_destino')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
