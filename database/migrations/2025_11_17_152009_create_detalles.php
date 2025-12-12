<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle', function (Blueprint $table) {
            $table->string('orden_id', 12)->primary();
            $table->integer('producto_id')->constrained('productos');
            $table->integer('kit_id')->constrained('kits');
            $table->integer('unidades');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles');
    }
};
