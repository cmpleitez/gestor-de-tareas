<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id');
            $table->foreignId('user_id');
            $table->timestamps();

            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_user');
    }
};
