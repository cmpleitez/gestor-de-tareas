<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('origen_stock_id')->constrained('stocks');
            $table->foreignId('oficina_id')->constrained('oficina_stock', 'oficina_id');
            $table->foreignId('destino_stock_id')->constrained('oficina_stock', 'stock_id');
            $table->foreignId('producto_id')->constrained('oficina_stock', 'producto_id');
            $table->string('entrada');
            $table->bigInteger('unidades');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};
