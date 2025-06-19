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
            $table->char('tarea_user_id', 12)->foreignId();
            $table->string('observacion')->nullable();
            $table->timestampsTz();

            $table->foreign('tarea_user_id')->references('id')->on('tarea_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
