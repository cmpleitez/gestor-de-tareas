<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepciones', function (Blueprint $table) {
            $table->char('id', 10)->primary();
            $table->foreignId('solicitud_id');
            $table->foreignId('oficina_id');
            $table->foreignId('user_id_origen');
            $table->foreignId('user_id_destino');
            $table->string('detalles');
            $table->string('observacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestampsTz();
            
            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('oficina_id')->references('id')->on('oficinas');
            $table->foreign('user_id_origen')->references('id')->on('users');
            $table->foreign('user_id_destino')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
