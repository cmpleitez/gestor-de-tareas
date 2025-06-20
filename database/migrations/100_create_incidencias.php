<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->char('id', 16)->primary();
            $table->char('actividad_id', 12)->foreignId();
            $table->string('observacion')->nullable();
            $table->timestampsTz();

            $table->foreign('actividad_id')->references('id')->on('actividades');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
