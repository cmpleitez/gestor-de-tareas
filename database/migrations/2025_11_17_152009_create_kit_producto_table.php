<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_producto', function (Blueprint $table) {
            $table->foreignId('kit_id')->constrained('kits');
            $table->foreignId('producto_id')->constrained('productos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_producto');
    }
};
