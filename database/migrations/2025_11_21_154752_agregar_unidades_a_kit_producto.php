<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kit_producto', function (Blueprint $table) {
            $table->integer('unidades')->default(1)->after('producto_id');
            $table->dropTimestamps();
        });
    }

    public function down(): void
    {
        Schema::table('kit_producto', function (Blueprint $table) {
            $table->dropColumn('unidades');
            $table->timestamps();
        });
    }
};
