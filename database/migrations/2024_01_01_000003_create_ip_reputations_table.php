<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ip_reputations', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique()->index();
            $table->decimal('reputation_score', 5, 2)->default(0);
            $table->string('risk_level', 20)->default('minimal');
            $table->decimal('confidence', 5, 2)->default(0);
            $table->json('data')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->integer('total_requests')->default(0);
            $table->integer('threat_requests')->default(0);
            $table->integer('benign_requests')->default(0);
            $table->decimal('request_frequency', 10, 2)->default(0);
            $table->json('geographic_data')->nullable();
            $table->json('network_data')->nullable();
            $table->json('behavioral_patterns')->nullable();
            $table->json('threat_indicators')->nullable();
            $table->boolean('whitelisted')->default(false);
            $table->boolean('blacklisted')->default(false);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['reputation_score', 'created_at']);
            $table->index(['risk_level', 'created_at']);
            $table->index(['whitelisted', 'created_at']);
            $table->index(['blacklisted', 'created_at']);
            $table->index(['request_frequency', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ip_reputations');
    }
};
