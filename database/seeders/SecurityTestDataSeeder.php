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

        // IPs de prueba con países reales
        $testIPs = [
            '203.0.113.1' => ['US', 'United States', 'New York', 40.7128, -74.0060],
            '198.51.100.1' => ['CN', 'China', 'Beijing', 39.9042, 116.4074],
            '192.0.2.1' => ['RU', 'Russia', 'Moscow', 55.7558, 37.6176],
            '203.0.113.2' => ['DE', 'Germany', 'Berlin', 52.5200, 13.4050],
            '198.51.100.2' => ['GB', 'United Kingdom', 'London', 51.5074, -0.1278],
            '192.0.2.2' => ['FR', 'France', 'Paris', 48.8566, 2.3522],
            '203.0.113.3' => ['JP', 'Japan', 'Tokyo', 35.6762, 139.6503],
            '198.51.100.3' => ['BR', 'Brazil', 'São Paulo', -23.5505, -46.6333],
            '192.0.2.3' => ['IN', 'India', 'Mumbai', 19.0760, 72.8777],
            '203.0.113.4' => ['AU', 'Australia', 'Sydney', -33.8688, 151.2093],
        ];

        // Categorías de amenazas realistas
        $categories = [
            'sql_injection',
            'xss_attack',
            'path_traversal',
            'command_injection',
            'brute_force',
            'suspicious_activity',
            'rate_limit_exceeded',
            'malware_detected',
            'phishing_attempt',
            'ddos_attack'
        ];

        // Razones de amenazas realistas
        $reasons = [
            'SQL injection attempt detected',
            'XSS attack pattern identified',
            'Path traversal attempt blocked',
            'Command injection detected',
            'Multiple failed login attempts',
            'Suspicious request pattern',
            'Rate limit exceeded for IP',
            'Malware signature detected',
            'Phishing URL detected',
            'DDoS attack pattern identified'
        ];

        // URIs de prueba realistas
        $testURIs = [
            '/admin/login.php?id=1\' OR 1=1--',
            '/search?q=<script>alert("xss")</script>',
            '/files/../../../etc/passwd',
            '/ping?host=127.0.0.1;cat /etc/passwd',
            '/admin/login.php',
            '/api/users?limit=1000',
            '/wp-admin/admin-ajax.php',
            '/cgi-bin/test.cgi',
            '/phpmyadmin/index.php',
            '/admin/config.php'
        ];

        // User agents realistas
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0) Gecko/20100101 Firefox/89.0',
            'curl/7.68.0',
            'python-requests/2.25.1',
            'Go-http-client/1.1',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
        ];

        $eventsCreated = 0;

        foreach ($testIPs as $ip => $geoData) {
            // Crear múltiples eventos por IP para simular actividad real
            $eventCount = rand(3, 8);

            for ($i = 0; $i < $eventCount; $i++) {
                $threatScore = $this->generateRealisticThreatScore();
                $category = $categories[array_rand($categories)];
                $reason = $reasons[array_rand($reasons)];
                $uri = $testURIs[array_rand($testURIs)];
                $userAgent = $userAgents[array_rand($userAgents)];

                // Generar timestamp realista (últimos 30 días)
                $timestamp = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                // Crear geolocalización realista
                $geolocation = [
                    'country' => $geoData[1],
                    'country_code' => $geoData[0],
                    'region' => $geoData[2],
                    'city' => $geoData[2],
                    'latitude' => $geoData[3],
                    'longitude' => $geoData[4],
                    'timezone' => 'UTC',
                    'isp' => 'Test ISP',
                    'org' => 'Test Organization',
                    'as' => 'AS12345',
                    'query' => $ip,
                    'source' => 'seeder',
                    'timestamp' => $timestamp->toISOString()
                ];

                // Crear payload realista
                $payload = $this->generateRealisticPayload($category, $uri);

                // Crear headers realistas
                $headers = [
                    'User-Agent' => $userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1'
                ];

                DB::table('security_events')->insert([
                    'ip_address' => $ip,
                    'user_id' => null, // No hay usuario autenticado en estos eventos
                    'session_id' => 'session_' . uniqid(),
                    'request_uri' => $uri,
                    'request_method' => $this->getRequestMethod($category),
                    'user_agent' => $userAgent,
                    'threat_score' => $threatScore,

                    'reason' => $reason,
                    'payload' => json_encode($payload),
                    'headers' => json_encode($headers),
                    'geolocation' => json_encode($geolocation),
                    'risk_level' => $this->getRiskLevel($threatScore),
                    'confidence' => rand(70, 95),
                    'source' => 'seeder',
                    'category' => $category,
                    'severity' => $this->getSeverity($threatScore),
                    'status' => 'open',
                    'metadata' => json_encode([
                        'seeded' => true,
                        'test_data' => true,
                        'created_at' => now()->toISOString()
                    ]),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $eventsCreated++;
            }
        }

        $this->command->info("   ✅ {$eventsCreated} eventos de seguridad creados con geolocalización");
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

    /**
     * Generar score de amenaza realista
     */
    private function generateRealisticThreatScore(): float
    {
        // Solo 3 niveles: Crítico, Alto y Medio
        $rand = rand(1, 100);

        if ($rand <= 40)
            return rand(40, 59);      // 40% - Medio
        if ($rand <= 80)
            return rand(60, 79);      // 40% - Alto
        return rand(80, 95);                        // 20% - Crítico
    }

    /**
     * Generar payload realista basado en categoría
     */
    private function generateRealisticPayload(string $category, string $uri): array
    {
        $payloads = [
            'sql_injection' => ['id' => '1\' OR 1=1--', 'query' => 'SELECT * FROM users'],
            'xss_attack' => ['q' => '<script>alert("xss")</script>', 'input' => 'javascript:alert(1)'],
            'path_traversal' => ['file' => '../../../etc/passwd', 'path' => '..\\..\\..\\windows\\system32'],
            'command_injection' => ['host' => '127.0.0.1;cat /etc/passwd', 'cmd' => 'ping;rm -rf /'],
            'brute_force' => ['username' => 'admin', 'password' => 'password123'],
            'suspicious_activity' => ['limit' => '1000', 'offset' => '0'],
            'rate_limit_exceeded' => ['requests' => '1000', 'timeframe' => '1 minute'],
            'malware_detected' => ['signature' => 'trojan.win32', 'hash' => 'abc123'],
            'phishing_attempt' => ['url' => 'http://fake-bank.com', 'domain' => 'fake-bank.com'],
            'ddos_attack' => ['requests_per_second' => '10000', 'pattern' => 'flood']
        ];

        return $payloads[$category] ?? ['data' => 'test'];
    }

    /**
     * Obtener método de request basado en categoría
     */
    private function getRequestMethod(string $category): string
    {
        $methods = [
            'sql_injection' => 'GET',
            'xss_attack' => 'GET',
            'path_traversal' => 'GET',
            'command_injection' => 'GET',
            'brute_force' => 'POST',
            'suspicious_activity' => 'GET',
            'rate_limit_exceeded' => 'GET',
            'malware_detected' => 'POST',
            'phishing_attempt' => 'GET',
            'ddos_attack' => 'GET'
        ];

        return $methods[$category] ?? 'GET';
    }

    /**
     * Obtener nivel de riesgo
     */
    private function getRiskLevel(float $score): string
    {
        if ($score >= 80)
            return 'critical';
        if ($score >= 60)
            return 'high';
        return 'medium'; // Solo 3 niveles: Crítico, Alto y Medio
    }

    /**
     * Obtener severidad
     */
    private function getSeverity(float $score): string
    {
        if ($score >= 80)
            return 'critical';
        if ($score >= 60)
            return 'high';
        return 'medium'; // Solo 3 niveles: Crítico, Alto y Medio
    }
}
