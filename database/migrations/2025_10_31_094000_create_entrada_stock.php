<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrada_stock', function (Blueprint $table) {
            $table->foreignId('entrada_id')->constrained('entradas');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->foreignId('producto_id')->constrained('productos');
            $table->bigInteger('unidades');
            $table->primary(['entrada_id', 'stock_id', 'producto_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrada_stock');
    }
};
