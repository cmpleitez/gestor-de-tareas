<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kits', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('kit')->unique();
            $table->decimal('precio', 20, 4);
            $table->string('imagen', 2048)->nullable();
            $table->integer('descargas')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kits');
    }
};
