<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atenciones', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->foreignId('oficina_id')->constrained('oficinas');
            $table->foreignId('estado_id')->constrained('estados');
            $table->string('detalle')->nullable();
            $table->decimal('avance', 5, 2)->default(0.00);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atenciones');
    }
};
