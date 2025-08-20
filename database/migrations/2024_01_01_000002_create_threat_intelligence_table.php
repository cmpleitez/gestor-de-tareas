<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('threat_intelligence', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique()->index();
            $table->decimal('threat_score', 5, 2)->default(0);
            $table->string('classification', 30)->default('unknown');
            $table->decimal('confidence', 5, 2)->default(0);
            $table->json('data')->nullable();
            $table->json('sources')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->string('threat_type', 50)->nullable();
            $table->string('malware_family', 100)->nullable();
            $table->json('attack_vectors')->nullable();
            $table->json('targeted_sectors')->nullable();
            $table->string('geographic_origin', 100)->nullable();
            $table->string('asn', 50)->nullable();
            $table->string('isp', 200)->nullable();
            $table->string('organization', 200)->nullable();
            $table->string('country_code', 3)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('verified')->default(false);
            $table->boolean('false_positive')->default(false);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['threat_score', 'created_at']);
            $table->index(['classification', 'created_at']);
            $table->index(['country_code', 'created_at']);
            $table->index(['threat_type', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('threat_intelligence');
    }
};
