<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepciones', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('atencion_id', 12)->constrained('atenciones');
            $table->foreignId('solicitud_id')->constrained('solicitudes');
            $table->foreignId('origen_user_id')->constrained('users');
            $table->foreignId('destino_user_id')->constrained('users');
            $table->foreignId('user_destino_role_id')->constrained('roles');
            $table->foreignId('estado_id')->constrained('estados');
            $table->boolean('validada_origen')->default(false);
            $table->boolean('validada_destino')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
