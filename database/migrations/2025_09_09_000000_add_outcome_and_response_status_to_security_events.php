<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('security_events', function (Blueprint $table) {
            $table->string('outcome', 20)->nullable()->after('status');
            $table->unsignedSmallInteger('response_status')->nullable()->after('outcome');

            $table->index('outcome');
            $table->index('response_status');
        });
    }

    public function down()
    {
        Schema::table('security_events', function (Blueprint $table) {
            $table->dropIndex(['outcome']);
            $table->dropIndex(['response_status']);
            $table->dropColumn(['outcome', 'response_status']);
        });
    }
};
