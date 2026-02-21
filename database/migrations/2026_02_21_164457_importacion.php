<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importacion', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('kit');
            $table->integer('kit_unidades')->default(1);
            $table->string('producto_codigo');
            $table->string('producto');
            $table->string('marca');
            $table->string('modelo');
            $table->string('tipo');
            $table->decimal('producto_precio', 20, 4)->default(0);
            $table->integer('stock_unidades')->default(0);            
            $table->string('equivalente_producto_codigo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importacion');
    }
};
