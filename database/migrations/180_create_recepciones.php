<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepciones', function (Blueprint $table) {
            $table->char('id', 12)->primary();
            $table->foreignId('solicitud_id');
            $table->foreignId('role_id');
            $table->foreignId('oficina_id');
            $table->foreignId('area_id');
            $table->foreignId('zona_id');
            $table->foreignId('distrito_id');
            $table->foreignId('user_id_origen');
            $table->foreignId('user_id_destino');
            $table->foreignId('estado_id');
            $table->char('atencion_id', 12)->foreignId();
            $table->string('detalles');
            $table->string('observacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('oficina_id')->references('id')->on('oficinas');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('zona_id')->references('id')->on('zonas');
            $table->foreign('distrito_id')->references('id')->on('distritos');
            $table->foreign('user_id_origen')->references('id')->on('users');
            $table->foreign('user_id_destino')->references('id')->on('users');
            $table->foreign('atencion_id')->references('id')->on('atenciones');
            $table->foreign('estado_id')->references('id')->on('estados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
