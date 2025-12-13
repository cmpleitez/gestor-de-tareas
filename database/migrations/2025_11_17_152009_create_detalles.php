<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles', function (Blueprint $table) {
            $table->string('orden_id', 12)->constrained('ordenes');
            $table->integer('producto_id')->constrained('productos');
            $table->integer('kit_id')->constrained('kits');
            $table->integer('unidades');
            $table->decimal('precio', 20, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles');
    }
};
