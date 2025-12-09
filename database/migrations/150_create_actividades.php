<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('recepcion_id', 12)->constrained('recepciones');
            $table->foreignId('tarea_id');
            $table->foreignId('user_destino_role_id');
            $table->foreignId('origen_user_id');
            $table->foreignId('destino_user_id');
            $table->foreignId('estado_id');
            $table->string('observacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->foreign('recepcion_id')->references('id')->on('recepciones');
            $table->foreign('tarea_id')->references('id')->on('tareas');
            $table->foreign('user_destino_role_id')->references('id')->on('roles');
            $table->foreign('origen_user_id')->references('id')->on('users');
            $table->foreign('destino_user_id')->references('id')->on('users');
            $table->foreign('estado_id')->references('id')->on('estados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
