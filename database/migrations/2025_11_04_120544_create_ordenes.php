<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->string('id', 12)->primary();
            $table->string('atencion_id', 12)->constrained('atenciones');
            $table->integer('kit_id')->constrained('kits');
            $table->integer('unidades');
            $table->decimal('precio', 20, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden');
    }
};
