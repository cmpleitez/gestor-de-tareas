<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign(['user_destino_role_id']);
            $table->dropForeign(['origen_user_id']);
            $table->dropForeign(['destino_user_id']);

            $table->dropColumn(['user_destino_role_id', 'origen_user_id', 'destino_user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->foreignId('user_destino_role_id')->after('tarea_id')->constrained('roles');
            $table->foreignId('origen_user_id')->after('user_destino_role_id')->constrained('users');
            $table->foreignId('destino_user_id')->after('origen_user_id')->constrained('users');
        });
    }
};
