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
            $table->foreignId('oficina_stock_id')->constrained('oficina_stock');
            $table->foreignId('oficina_id')->constrained('oficinas');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->foreignId('producto_id')->constrained('productos');
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
