<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id');
            $table->foreignId('role_id')->default(7);

            $table->string('dui',9)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('area_id')->references('id')->on('areas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
