<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atenciones', function (Blueprint $table) {
            $table->char('id', 12)->primary();
            $table->foreignId('estado_id');
            $table->foreignId('solicitud_id');
            $table->timestampsTz();

            $table->foreign('estado_id')->references('id')->on('estados');
            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atenciones');
    }
};
