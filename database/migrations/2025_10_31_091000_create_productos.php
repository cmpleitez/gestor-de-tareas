<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->string('producto')->unique();
            $table->boolean('accesorio')->default(false);
            $table->decimal('precio', 20, 4);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
