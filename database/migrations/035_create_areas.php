<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oficina_id');
            $table->string('area', 128)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('oficina_id')->references('id')->on('oficinas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
