<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Añadir nuevos campos
            $table->integer('stock_origen_resultante')->after('unidades');
            $table->integer('stock_destino_resultante')->after('stock_origen_resultante');
            
            // Eliminar campo obsoleto
            $table->dropColumn('stock_resultante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->integer('stock_resultante')->after('unidades');
            $table->dropColumn(['stock_origen_resultante', 'stock_destino_resultante']);
        });
    }
};
