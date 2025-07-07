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
            $table->foreignId('zona_id');
            $table->string('area', 128)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('zona_id')->references('id')->on('zonas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
