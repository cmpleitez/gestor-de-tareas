<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SecurityTestDataSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        $this->command->info('🌱 Generando datos de prueba para el sistema de seguridad...');

        // 1. Crear eventos de seguridad de prueba
        $this->createSecurityEvents();

        // 2. Crear datos de reputación de IPs de prueba
        $this->createIPReputationData();

        // 3. Crear datos de inteligencia de amenazas de prueba
        $this->createThreatIntelligenceData();

        $this->command->info('✅ Datos de prueba generados exitosamente');
    }

    /**
     * Crear eventos de seguridad de prueba
     */
    private function createSecurityEvents(): void
    {
        if (!Schema::hasTable('security_events')) {
            $this->command->warn('   ⚠️  Tabla security_events no existe, saltando...');
            return;
        }

        $this->command->info('   📊 Creando eventos de seguridad de prueba...');

        $eventTypes = ['login_attempt', 'suspicious_request', 'ip_block', 'threat_detected', 'rate_limit_exceeded'];
        $ipAddresses = ['192.168.1.100', '10.0.0.50', '172.16.0.25', '203.0.113.10', '198.51.100.5'];

        for ($i = 0; $i < 50; $i++) {
            DB::table('security_events')->insert([
                'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                'category' => $eventTypes[array_rand($eventTypes)], // ✅ COLUMNA REAL
                'threat_score' => rand(10, 95),
                'action_taken' => ['allow', 'block', 'challenge'][array_rand(['allow', 'block', 'challenge'])],
                'request_uri' => '/api/security/check', // ✅ COLUMNA OBLIGATORIA
                'request_method' => 'GET', // ✅ COLUMNA OBLIGATORIA
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', // ✅ COLUMNA OBLIGATORIA
                'payload' => json_encode([ // ✅ COLUMNA REAL
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'request_path' => '/api/security/check',
                    'timestamp' => Carbon::now()->subMinutes(rand(1, 1440))->toISOString(),
                ]),
                'created_at' => Carbon::now()->subMinutes(rand(1, 1440)),
                'updated_at' => Carbon::now()->subMinutes(rand(1, 1440)),
            ]);
        }

        $this->command->info('   ✅ 50 eventos de seguridad creados');
    }

    /**
     * Crear datos de reputación de IPs de prueba
     */
    private function createIPReputationData(): void
    {
        if (!Schema::hasTable('ip_reputations')) {
            $this->command->warn('   ⚠️  Tabla ip_reputations no existe, saltando...');
            return;
        }

        $this->command->info('   🌐 Creando datos de reputación de IPs de prueba...');

        $ips = [
            ['ip' => '192.168.1.100', 'reputation_score' => 15, 'risk_level' => 'low'],
            ['ip' => '10.0.0.50', 'reputation_score' => 25, 'risk_level' => 'low'],
            ['ip' => '172.16.0.25', 'reputation_score' => 45, 'risk_level' => 'medium'],
            ['ip' => '203.0.113.10', 'reputation_score' => 75, 'risk_level' => 'high'],
            ['ip' => '198.51.100.5', 'reputation_score' => 85, 'risk_level' => 'critical'],
            ['ip' => '8.8.8.8', 'reputation_score' => 5, 'risk_level' => 'minimal'],
            ['ip' => '1.1.1.1', 'reputation_score' => 8, 'risk_level' => 'minimal'],
            ['ip' => '208.67.222.222', 'reputation_score' => 12, 'risk_level' => 'low'],
        ];

        foreach ($ips as $ipData) {
            DB::table('ip_reputations')->insert([
                'ip_address' => $ipData['ip'],
                'reputation_score' => $ipData['reputation_score'], // ✅ COLUMNA REAL
                'risk_level' => $ipData['risk_level'], // ✅ COLUMNA REAL
                'geographic_data' => json_encode([ // ✅ COLUMNA REAL (JSON)
                    'country' => 'US',
                    'city' => 'Unknown',
                    'region' => 'Unknown',
                ]),
                'network_data' => json_encode([ // ✅ COLUMNA REAL (JSON)
                    'isp' => 'Unknown ISP',
                    'asn' => 'Unknown ASN',
                ]),
                'blacklisted' => $ipData['reputation_score'] > 70,
                'whitelisted' => $ipData['reputation_score'] < 20,
                'last_seen' => Carbon::now()->subMinutes(rand(1, 1440)),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subMinutes(rand(1, 1440)),
            ]);
        }
        $this->command->info('   ✅ 8 registros de reputación de IPs creados');
    }

    /**
     * Crear datos de inteligencia de amenazas de prueba
     */
    private function createThreatIntelligenceData(): void
    {
        if (!Schema::hasTable('threat_intelligence')) {
            $this->command->warn('   ⚠️  Tabla threat_intelligence no existe, saltando...');
            return;
        }
        
        $this->command->info('   🚨 Creando datos de inteligencia de amenazas de prueba...');
        
        $threats = [
            ['type' => 'malware', 'ip' => '192.168.1.100'],
            ['type' => 'phishing', 'ip' => '10.0.0.50'],
            ['type' => 'ddos', 'ip' => '172.16.0.25'],
            ['type' => 'sql_injection', 'ip' => '203.0.113.10'],
            ['type' => 'xss', 'ip' => '198.51.100.5']
        ];
        
        foreach ($threats as $threat) {
            DB::table('threat_intelligence')->insert([
                'threat_type' => $threat['type'], // ✅ COLUMNA REAL
                'ip_address' => $threat['ip'], // ✅ COLUMNA OBLIGATORIA
                'status' => 'active', // ✅ COLUMNA REAL
                'created_at' => Carbon::now()->subHours(rand(1, 24)),
                'updated_at' => Carbon::now()->subHours(rand(1, 24))
            ]);
        }
        
        $this->command->info('   ✅ 5 amenazas de inteligencia creadas');
    }
}
