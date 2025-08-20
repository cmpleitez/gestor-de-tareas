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
            $table->string('session_id', 255)->nullable()->index();
            $table->text('request_uri');
            $table->string('request_method', 10);
            $table->text('user_agent')->nullable();
            $table->decimal('threat_score', 5, 2)->default(0);
            $table->string('action_taken', 50)->nullable();
            $table->text('reason')->nullable();
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->json('geolocation')->nullable();
            $table->string('risk_level', 20)->default('minimal');
            $table->decimal('confidence', 5, 2)->default(0);
            $table->string('source', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->string('severity', 20)->default('info');
            $table->string('status', 20)->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
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
