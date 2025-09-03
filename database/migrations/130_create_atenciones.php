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
            $table->char('solicitud_id', 12)->constrained('solicitudes');
            $table->char('oficina_id', 12)->constrained('oficinas');
            $table->char('estado_id', 12)->constrained('estados');
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
