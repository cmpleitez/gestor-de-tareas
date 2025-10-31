<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_kit', function (Blueprint $table) {
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('kit_id')->constrained('kits');
            $table->primary(['producto_id', 'kit_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_kit');
    }
};
