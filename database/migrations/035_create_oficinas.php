<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id');
            $table->string('oficina', 128)->unique();
            $table->boolean('activo')->default(true);
            $table->timestampsTz();

            $table->foreign('area_id')->references('id')->on('areas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficinas');
    }
};
