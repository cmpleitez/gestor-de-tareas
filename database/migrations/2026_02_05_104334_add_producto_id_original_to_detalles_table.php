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
        Schema::table('detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('producto_id_original')->nullable()->after('producto_id');
            $table->foreign('producto_id_original')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles', function (Blueprint $table) {
            $table->dropForeign(['producto_id_original']);
            $table->dropColumn('producto_id_original');
        });
    }
};
