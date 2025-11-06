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
            $table->foreignId('oficina_id')->constrained('oficinas');
            $table->foreignId('user_id')->constrained('users');
            $table->string('entrada');
            $table->bigInteger('unidades');
            $table->string('url_tracking')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};
