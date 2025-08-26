<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ThreatIntelligence;

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

        // 4. Crear logs de seguridad de prueba
        $this->createSecurityLogsData();

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

        // IPs de prueba con países reales (UTF-8 encoded)
        $testIPs = [
            '203.0.113.1' => ['US', mb_convert_encoding('United States', 'UTF-8', 'auto'), mb_convert_encoding('New York', 'UTF-8', 'auto'), 40.7128, -74.0060],
            '198.51.100.1' => ['CN', mb_convert_encoding('China', 'UTF-8', 'auto'), mb_convert_encoding('Beijing', 'UTF-8', 'auto'), 39.9042, 116.4074],
            '192.0.2.1' => ['RU', mb_convert_encoding('Russia', 'UTF-8', 'auto'), mb_convert_encoding('Moscow', 'UTF-8', 'auto'), 55.7558, 37.6176],
            '203.0.113.2' => ['DE', mb_convert_encoding('Germany', 'UTF-8', 'auto'), mb_convert_encoding('Berlin', 'UTF-8', 'auto'), 52.5200, 13.4050],
            '198.51.100.2' => ['GB', mb_convert_encoding('United Kingdom', 'UTF-8', 'auto'), mb_convert_encoding('London', 'UTF-8', 'auto'), 51.5074, -0.1278],
            '192.0.2.2' => ['FR', mb_convert_encoding('France', 'UTF-8', 'auto'), mb_convert_encoding('Paris', 'UTF-8', 'auto'), 48.8566, 2.3522],
            '203.0.113.3' => ['JP', mb_convert_encoding('Japan', 'UTF-8', 'auto'), mb_convert_encoding('Tokyo', 'UTF-8', 'auto'), 35.6762, 139.6503],
            '198.51.100.3' => ['BR', mb_convert_encoding('Brazil', 'UTF-8', 'auto'), mb_convert_encoding('São Paulo', 'UTF-8', 'auto'), -23.5505, -46.6333],
            '192.0.2.3' => ['IN', mb_convert_encoding('India', 'UTF-8', 'auto'), mb_convert_encoding('Mumbai', 'UTF-8', 'auto'), 19.0760, 72.8777],
            '203.0.113.4' => ['AU', mb_convert_encoding('Australia', 'UTF-8', 'auto'), mb_convert_encoding('Sydney', 'UTF-8', 'auto'), -33.8688, 151.2093],
        ];

        // Categorías de amenazas realistas (UTF-8 encoded)
        $categories = [
            mb_convert_encoding('sql_injection', 'UTF-8', 'auto'),
            mb_convert_encoding('xss_attack', 'UTF-8', 'auto'),
            mb_convert_encoding('path_traversal', 'UTF-8', 'auto'),
            mb_convert_encoding('command_injection', 'UTF-8', 'auto'),
            mb_convert_encoding('brute_force', 'UTF-8', 'auto'),
            mb_convert_encoding('suspicious_activity', 'UTF-8', 'auto'),
            mb_convert_encoding('rate_limit_exceeded', 'UTF-8', 'auto'),
            mb_convert_encoding('malware_detected', 'UTF-8', 'auto'),
            mb_convert_encoding('phishing_attempt', 'UTF-8', 'auto'),
            mb_convert_encoding('ddos_attack', 'UTF-8', 'auto')
        ];

        // Razones de amenazas realistas (UTF-8 encoded)
        $reasons = [
            mb_convert_encoding('SQL injection attempt detected', 'UTF-8', 'auto'),
            mb_convert_encoding('XSS attack pattern identified', 'UTF-8', 'auto'),
            mb_convert_encoding('Path traversal attempt blocked', 'UTF-8', 'auto'),
            mb_convert_encoding('Command injection detected', 'UTF-8', 'auto'),
            mb_convert_encoding('Multiple failed login attempts', 'UTF-8', 'auto'),
            mb_convert_encoding('Suspicious request pattern', 'UTF-8', 'auto'),
            mb_convert_encoding('Rate limit exceeded for IP', 'UTF-8', 'auto'),
            mb_convert_encoding('Malware signature detected', 'UTF-8', 'auto'),
            mb_convert_encoding('Phishing URL detected', 'UTF-8', 'auto'),
            mb_convert_encoding('DDoS attack pattern identified', 'UTF-8', 'auto')
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

                // Generar timestamp realista (últimos 3 días incluyendo hoy)
                $timestamp = Carbon::now()->subDays(rand(0, 2))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                // Crear geolocalización realista (UTF-8 encoded)
                $geolocation = [
                    'country' => mb_convert_encoding($geoData[1], 'UTF-8', 'auto'),
                    'country_code' => mb_convert_encoding($geoData[0], 'UTF-8', 'auto'),
                    'region' => mb_convert_encoding($geoData[2], 'UTF-8', 'auto'),
                    'city' => mb_convert_encoding($geoData[2], 'UTF-8', 'auto'),
                    'latitude' => $geoData[3],
                    'longitude' => $geoData[4],
                    'timezone' => mb_convert_encoding('UTC', 'UTF-8', 'auto'),
                    'isp' => mb_convert_encoding('Test ISP', 'UTF-8', 'auto'),
                    'org' => mb_convert_encoding('Test Organization', 'UTF-8', 'auto'),
                    'as' => mb_convert_encoding('AS12345', 'UTF-8', 'auto'),
                    'query' => $ip,
                    'source' => mb_convert_encoding('seeder', 'UTF-8', 'auto'),
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
                'geographic_data' => json_encode([ // ✅ COLUMNA REAL (JSON) - UTF-8 encoded
                    'country' => mb_convert_encoding('US', 'UTF-8', 'auto'),
                    'city' => mb_convert_encoding('Unknown', 'UTF-8', 'auto'),
                    'region' => mb_convert_encoding('Unknown', 'UTF-8', 'auto'),
                ]),
                'network_data' => json_encode([ // ✅ COLUMNA REAL (JSON) - UTF-8 encoded
                    'isp' => mb_convert_encoding('Unknown ISP', 'UTF-8', 'auto'),
                    'asn' => mb_convert_encoding('Unknown ASN', 'UTF-8', 'auto'),
                ]),
                'blacklisted' => $ipData['reputation_score'] > 70,
                'whitelisted' => $ipData['reputation_score'] < 20,
                'last_seen' => Carbon::now()->subDays(rand(0, 2))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'created_at' => Carbon::now()->subDays(rand(0, 2)), // Últimos 3 días (0, 1, 2)
                'updated_at' => Carbon::now()->subDays(rand(0, 2))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
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
            [
                'ip_address' => '203.0.113.10',
                'threat_score' => 85.50,
                'classification' => 'malware',
                'confidence' => 85.00,
                'threat_type' => 'trojan',
                'malware_family' => 'Trojan.Win32.Generic',
                'status' => 'active',
                'verified' => true,
                'false_positive' => false
            ],
            [
                'ip_address' => '198.51.100.15',
                'threat_score' => 92.75,
                'classification' => 'phishing',
                'confidence' => 90.00,
                'threat_type' => 'social_engineering',
                'malware_family' => null,
                'status' => 'active',
                'verified' => true,
                'false_positive' => false
            ],
            [
                'ip_address' => '192.0.2.25',
                'threat_score' => 78.30,
                'classification' => 'ddos',
                'confidence' => 75.00,
                'threat_type' => 'network_attack',
                'malware_family' => null,
                'status' => 'active',
                'verified' => false,
                'false_positive' => false
            ],
            [
                'ip_address' => '10.0.0.100',
                'threat_score' => 65.20,
                'classification' => 'sql_injection',
                'confidence' => 80.00,
                'threat_type' => 'web_attack',
                'malware_family' => null,
                'status' => 'blocked',
                'verified' => true,
                'false_positive' => false
            ],
            [
                'ip_address' => '172.16.0.50',
                'threat_score' => 58.90,
                'classification' => 'xss',
                'confidence' => 70.00,
                'threat_type' => 'web_attack',
                'malware_family' => null,
                'status' => 'blocked',
                'verified' => false,
                'false_positive' => false
            ]
        ];

        foreach ($threats as $threat) {
            DB::table('threat_intelligence')->insert([
                'ip_address' => $threat['ip_address'],
                'threat_score' => $threat['threat_score'],
                'classification' => $threat['classification'],
                'confidence' => $threat['confidence'],
                'data' => json_encode([
                    'description' => 'Threat detected from seeder data',
                    'source' => 'seeder',
                    'created_at' => now()->toISOString()
                ]),
                'sources' => json_encode(['seeder', 'test_data']),
                'threat_type' => $threat['threat_type'],
                'malware_family' => $threat['malware_family'],
                'attack_vectors' => json_encode(['web', 'network']),
                'targeted_sectors' => json_encode(['general']),
                'geographic_origin' => 'Unknown',
                'asn' => 'AS12345',
                'isp' => 'Test ISP',
                'organization' => 'Test Organization',
                'country_code' => 'XX',
                'region' => 'Unknown',
                'city' => 'Unknown',
                'latitude' => 0.00000000,
                'longitude' => 0.00000000,
                'timezone' => 'UTC',
                'status' => $threat['status'],
                'verified' => $threat['verified'],
                'false_positive' => $threat['false_positive'],
                'notes' => 'Test data generated by seeder',
                'metadata' => json_encode([
                    'seeded' => true,
                    'test_data' => true,
                    'created_at' => now()->toISOString()
                ]),
                'first_seen' => Carbon::now()->subDays(rand(0, 2)),
                'last_seen' => Carbon::now()->subDays(rand(0, 2)),
                'last_updated' => Carbon::now()->subDays(rand(0, 2)),
                'created_at' => Carbon::now()->subDays(rand(0, 2)),
                'updated_at' => Carbon::now()->subDays(rand(0, 2))
            ]);
        }

        $this->command->info('   ✅ 5 registros de inteligencia de amenazas creados');
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

    /**
     * Generar IP realista para amenazas
     */
    private function generateRealisticIP(): string
    {
        $ranges = [
            '203.0.113',
            '198.51.100',
            '192.0.2',
            '10.0.0',
            '172.16.0',
            '192.168.1',
            '185.199.108',
            '104.21.92',
            '45.33.12'
        ];

        $range = $ranges[array_rand($ranges)];
        $lastOctet = rand(1, 254);

        return "{$range}.{$lastOctet}";
    }

    /**
     * Generar pool de IPs únicas predefinidas
     */
    private function generateUniqueIPPool(): array
    {
        $ips = [];
        $ranges = [
            '203.0.113',
            '198.51.100', 
            '192.0.2',
            '10.0.0',
            '172.16.0',
            '192.168.1',
            '185.199.108',
            '104.21.92',
            '45.33.12',
            '8.8.8',
            '1.1.1',
            '208.67.222'
        ];

        // Generar 200 IPs únicas (suficiente para todas las amenazas)
        for ($i = 0; $i < 200; $i++) {
            $range = $ranges[$i % count($ranges)];
            $lastOctet = ($i % 254) + 1;
            $ips[] = "{$range}.{$lastOctet}";
        }

        return $ips;
    }

    /**
     * Generar IP de respaldo si se agota el pool
     */
    private function generateFallbackIP(int $index): string
    {
        $ranges = ['203.0.113', '198.51.100', '192.0.2'];
        $range = $ranges[$index % count($ranges)];
        $lastOctet = ($index % 254) + 1;
        return "{$range}.{$lastOctet}_fallback_{$index}";
    }

    /**
     * Obtener geolocalización real para una IP
     */
    private function getRealGeolocation(string $ip): array
    {
        try {
            // Usar el servicio de geolocalización
            $geolocationService = app(\App\Services\GeolocationService::class);
            $geolocation = $geolocationService->getGeolocation($ip);

            // Asegurar que country_code tenga máximo 3 caracteres
            if (isset($geolocation['country_code']) && strlen($geolocation['country_code']) > 3) {
                $geolocation['country_code'] = substr($geolocation['country_code'], 0, 3);
            }

            return $geolocation;
        } catch (\Exception $e) {
            // Fallback a datos básicos si falla el servicio
            return [
                'country' => 'Unknown',
                'country_code' => 'XX',
                'region' => 'Unknown',
                'city' => 'Unknown',
                'latitude' => 0,
                'longitude' => 0,
                'timezone' => 'UTC',
                'isp' => 'Unknown',
                'organization' => 'Unknown',
                'asn' => 'Unknown'
            ];
        }
    }

    /**
     * Crear logs de seguridad de prueba
     */
    private function createSecurityLogsData(): void
    {
        $this->command->info('   📝 Creando logs de seguridad de prueba...');

        // Crear directorio de logs si no existe
        $logsPath = storage_path('logs');
        if (!is_dir($logsPath)) {
            mkdir($logsPath, 0755, true);
        }

        // Generar logs de seguridad
        $this->createSecurityLogFile($logsPath);
        
        // Generar logs de firewall
        $this->createFirewallLogFile($logsPath);
        
        // Generar logs de IDS
        $this->createIDSLogFile($logsPath);

        $this->command->info('   ✅ Logs de seguridad creados con conjunto de caracteres utf8mb4');
    }

    /**
     * Crear archivo de log de seguridad
     */
    private function createSecurityLogFile(string $logsPath): void
    {
        $logFile = $logsPath . '/security.log';
        $logContent = '';
        
        // Generar 50 entradas de log de seguridad
        for ($i = 0; $i < 50; $i++) {
            $timestamp = Carbon::now()->subDays(rand(0, 2))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $ip = $this->generateRealisticIP();
            $threatType = ['malware', 'phishing', 'ddos', 'sql_injection', 'xss'][array_rand([0,1,2,3,4])];
            $severity = ['CRITICAL', 'HIGH', 'MEDIUM'][array_rand([0,1,2])];
            
            $logEntry = sprintf(
                "[%s] %s - %s threat detected from IP %s - Severity: %s - Source: seeder\n",
                $timestamp->format('Y-m-d H:i:s'),
                strtoupper($threatType),
                ucfirst($threatType),
                $ip,
                $severity
            );
            
            $logContent .= $logEntry;
        }
        
        // Escribir con conjunto de caracteres utf8mb4
        file_put_contents($logFile, $logContent, LOCK_EX);
    }

    /**
     * Crear archivo de log de firewall
     */
    private function createFirewallLogFile(string $logsPath): void
    {
        $logFile = $logsPath . '/firewall.log';
        $logContent = '';
        
        // Generar 30 entradas de log de firewall
        for ($i = 0; $i < 30; $i++) {
            $timestamp = Carbon::now()->subDays(rand(0, 2))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $ip = $this->generateRealisticIP();
            $action = ['BLOCKED', 'ALLOWED', 'QUARANTINED'][array_rand([0,1,2])];
            $reason = ['suspicious_activity', 'blacklisted_ip', 'rate_limit_exceeded'][array_rand([0,1,2])];
            
            $logEntry = sprintf(
                "[%s] FIREWALL %s request from IP %s - Reason: %s - Source: seeder\n",
                $timestamp->format('Y-m-d H:i:s'),
                $action,
                $ip,
                $reason
            );
            
            $logContent .= $logEntry;
        }
        
        // Escribir con conjunto de caracteres utf8mb4
        file_put_contents($logFile, $logContent, LOCK_EX);
    }

    /**
     * Crear archivo de log de IDS
     */
    private function createIDSLogFile(string $logsPath): void
    {
        $logFile = $logsPath . '/ids.log';
        $logContent = '';
        
        // Generar 40 entradas de log de IDS
        for ($i = 0; $i < 40; $i++) {
            $timestamp = Carbon::now()->subDays(rand(0, 2))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $ip = $this->generateRealisticIP();
            $attackType = ['SQL_INJECTION', 'XSS_ATTACK', 'PATH_TRAVERSAL', 'COMMAND_INJECTION'][array_rand([0,1,2,3])];
            $confidence = rand(70, 95);
            
            $logEntry = sprintf(
                "[%s] IDS ALERT - %s detected from IP %s - Confidence: %d%% - Source: seeder\n",
                $timestamp->format('Y-m-d H:i:s'),
                $attackType,
                $ip,
                $confidence
            );
            
            $logContent .= $logEntry;
        }
        
        // Escribir con conjunto de caracteres utf8mb4
        file_put_contents($logFile, $logContent, LOCK_EX);
    }
}
