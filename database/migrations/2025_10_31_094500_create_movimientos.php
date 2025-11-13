<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('oficina_id')->constrained('oficinas');
            $table->foreignId('origen_stock_id')->constrained('stocks');
            $table->foreignId('destino_stock_id')->constrained('stocks');
            $table->foreignId('producto_id')->constrained('productos');
            $table->string('movimiento');
            $table->bigInteger('unidades');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
