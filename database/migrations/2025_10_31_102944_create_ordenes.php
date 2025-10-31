<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->string('atencion_id', 12);
            $table->foreign('atencion_id')->references('id')->on('atenciones');
            $table->foreignId('kit_id')->constrained('kits');
            $table->primary(['atencion_id', 'kit_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
