<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->decimal('threat_score', 5, 2)->default(0);
            $table->json('geolocation')->nullable();
            $table->string('risk_level', 20)->default('minimal');
            $table->string('category', 50)->nullable();
            $table->string('severity', 20)->default('info');
            $table->string('status', 20)->default('open');
            $table->timestamps();

            $table->index(['ip_address', 'created_at']);
            $table->index(['threat_score', 'created_at']);
            $table->index(['risk_level', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_events');
    }
};
