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
            $table->char('atencion_id', 12)->constrained('atenciones');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('solicitud_id')->constrained('solicitudes');
            $table->foreignId('user_id_origen')->constrained('users');
            $table->foreignId('user_id_destino')->constrained('users');
            $table->foreignId('estado_id')->constrained('estados');
            $table->string('detalle');
            $table->string('observacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
