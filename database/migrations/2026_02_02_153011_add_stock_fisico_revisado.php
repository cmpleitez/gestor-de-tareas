<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalles', function (Blueprint $table) {
            $table->boolean('stock_fisico_revisado')->nullable()->default(null)->after('stock_fisico_existencias');
        });
    }

    public function down(): void
    {
        Schema::table('detalles', function (Blueprint $table) {
            $table->dropColumn('stock_fisico_revisado');
        });
    }
};
