<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityEvent;
use App\Models\User;
use Carbon\Carbon;

class SecurityEventSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🌐 Creando eventos de seguridad de prueba...');

        // IPs de ejemplo para diferentes escenarios
        $testIPs = [
            '192.168.1.100' => ['risk' => 'low', 'category' => 'normal_traffic'],
            '10.0.0.50' => ['risk' => 'medium', 'category' => 'suspicious_activity'],
            '172.16.0.25' => ['risk' => 'high', 'category' => 'potential_threat'],
            '203.0.113.10' => ['risk' => 'critical', 'category' => 'malicious_attack'],
            '198.51.100.75' => ['risk' => 'medium', 'category' => 'scanning_activity'],
            '45.33.12.200' => ['risk' => 'low', 'category' => 'normal_traffic'],
            '185.199.108.154' => ['risk' => 'high', 'category' => 'bot_activity'],
            '104.21.92.193' => ['risk' => 'medium', 'category' => 'suspicious_activity']
        ];

        // Categorías de eventos
        $categories = [
            'normal_traffic' => ['threat_score' => [0, 20], 'severity' => 'info'],
            'suspicious_activity' => ['threat_score' => [30, 50], 'severity' => 'low'],
            'potential_threat' => ['threat_score' => [60, 75], 'severity' => 'medium'],
            'malicious_attack' => ['threat_score' => [80, 95], 'severity' => 'critical'],
            'scanning_activity' => ['threat_score' => [40, 65], 'severity' => 'medium'],
            'bot_activity' => ['threat_score' => [70, 85], 'severity' => 'high']
        ];

        // Métodos HTTP
        $httpMethods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS'];

        // User agents de ejemplo
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
            'python-requests/2.28.1',
            'curl/7.68.0',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Mozilla/5.0 (compatible; Bingbot/2.0; +http://www.bing.com/bingbot.htm)',
            'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)'
        ];

        // URIs de ejemplo
        $uris = [
            '/',
            '/login',
            '/admin',
            '/api/users',
            '/wp-admin',
            '/phpmyadmin',
            '/.env',
            '/config.php',
            '/robots.txt',
            '/sitemap.xml'
        ];

        // Acciones tomadas
        $actions = [
            'allow' => 'Traffic allowed',
            'block' => 'IP blocked due to suspicious activity',
            'challenge' => 'CAPTCHA challenge presented',
            'monitor' => 'Enhanced monitoring enabled',
            'rate_limit' => 'Rate limiting applied',
            'log' => 'Event logged for analysis'
        ];

        // Fuentes de eventos
        $sources = [
            'firewall' => 'Firewall rule triggered',
            'waf' => 'Web Application Firewall',
            'ids' => 'Intrusion Detection System',
            'manual' => 'Manual review',
            'ml_detection' => 'Machine Learning detection',
            'threat_intel' => 'Threat intelligence feed'
        ];

        $eventsCreated = 0;
        $startDate = Carbon::now()->subDays(30);

        foreach ($testIPs as $ip => $config) {
            $category = $config['category'];
            $riskLevel = $config['risk'];
            $categoryConfig = $categories[$category];

            // Generar entre 5 y 20 eventos por IP
            $eventCount = rand(5, 20);

            for ($i = 0; $i < $eventCount; $i++) {
                $threatScore = rand($categoryConfig['threat_score'][0], $categoryConfig['threat_score'][1]);
                $severity = $categoryConfig['severity'];
                
                // Determinar acción basada en threat score
                $action = $this->determineAction($threatScore);
                $actionReason = $actions[$action];

                // Generar timestamp aleatorio en los últimos 30 días
                $timestamp = $startDate->copy()->addSeconds(rand(0, 30 * 24 * 3600));

                // Crear evento
                SecurityEvent::create([
                    'ip_address' => $ip,
                    'user_id' => $this->getRandomUserId(),
                    'session_id' => $this->generateSessionId(),
                    'request_uri' => $uris[array_rand($uris)],
                    'request_method' => $httpMethods[array_rand($httpMethods)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'threat_score' => $threatScore,
                    'action_taken' => $action,
                    'reason' => $actionReason,
                    'payload' => $this->generatePayload($threatScore),
                    'headers' => $this->generateHeaders(),
                    'geolocation' => $this->generateGeolocation($ip),
                    'risk_level' => $this->calculateRiskLevel($threatScore),
                    'confidence' => rand(60, 95),
                    'source' => array_rand($sources),
                    'category' => $category,
                    'severity' => $severity,
                    'status' => $this->determineStatus($threatScore),
                    'resolved_at' => $this->determineResolvedAt($threatScore),
                    'resolved_by' => $this->getRandomUserId(),
                    'notes' => $this->generateNotes($threatScore, $category),
                    'metadata' => $this->generateMetadata($threatScore),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);

                $eventsCreated++;
            }
        }

        $this->command->info("✅ {$eventsCreated} eventos de seguridad creados exitosamente");
    }

    private function determineAction(float $threatScore): string
    {
        if ($threatScore >= 80) return 'block';
        if ($threatScore >= 60) return 'challenge';
        if ($threatScore >= 40) return 'monitor';
        if ($threatScore >= 20) return 'rate_limit';
        return 'allow';
    }

    private function determineStatus(float $threatScore): string
    {
        if ($threatScore >= 80) return 'resolved';
        if ($threatScore >= 60) return 'under_review';
        if ($threatScore >= 40) return 'monitoring';
        return 'open';
    }

    private function determineResolvedAt(float $threatScore): ?string
    {
        if ($threatScore >= 80) {
            return Carbon::now()->subDays(rand(1, 7))->toDateTimeString();
        }
        return null;
    }

    private function calculateRiskLevel(float $threatScore): string
    {
        if ($threatScore >= 80) return 'critical';
        if ($threatScore >= 60) return 'high';
        if ($threatScore >= 40) return 'medium';
        if ($threatScore >= 20) return 'low';
        return 'minimal';
    }

    private function generatePayload(float $threatScore): array
    {
        if ($threatScore >= 60) {
            // Payloads sospechosos
            return [
                'sql_injection' => $threatScore >= 80 ? "' OR 1=1--" : null,
                'xss_attempt' => $threatScore >= 70 ? "<script>alert('xss')</script>" : null,
                'path_traversal' => $threatScore >= 75 ? "../../../etc/passwd" : null,
                'command_injection' => $threatScore >= 85 ? "; rm -rf /" : null
            ];
        }

        // Payloads normales
        return [
            'username' => 'test_user',
            'email' => 'test@example.com',
            'message' => 'Hello world'
        ];
    }

    private function generateHeaders(): array
    {
        return [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Encoding' => 'gzip, deflate',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1'
        ];
    }

    private function generateGeolocation(string $ip): array
    {
        $countries = ['US', 'CN', 'RU', 'DE', 'GB', 'FR', 'JP', 'BR'];
        $regions = ['California', 'Beijing', 'Moscow', 'Berlin', 'London', 'Paris', 'Tokyo', 'São Paulo'];
        $cities = ['Los Angeles', 'Beijing', 'Moscow', 'Berlin', 'London', 'Paris', 'Tokyo', 'São Paulo'];

        $index = array_rand($countries);
        
        return [
            'country_code' => $countries[$index],
            'country_name' => $this->getCountryName($countries[$index]),
            'region' => $regions[$index],
            'city' => $cities[$index],
            'latitude' => rand(-90, 90) + (rand(0, 100) / 100),
            'longitude' => rand(-180, 180) + (rand(0, 100) / 100),
            'timezone' => 'UTC'
        ];
    }

    private function generateNotes(float $threatScore, string $category): string
    {
        $notes = [
            'normal_traffic' => 'Traffic appears normal, no immediate concerns.',
            'suspicious_activity' => 'Unusual pattern detected, requires monitoring.',
            'potential_threat' => 'Multiple indicators suggest potential threat.',
            'malicious_attack' => 'Clear evidence of malicious intent detected.',
            'scanning_activity' => 'Port scanning or vulnerability scanning detected.',
            'bot_activity' => 'Automated bot behavior identified.'
        ];

        return $notes[$category] ?? 'Event logged for analysis.';
    }

    private function generateMetadata(float $threatScore): array
    {
        return [
            'detection_method' => $threatScore >= 60 ? 'ml_algorithm' : 'rule_based',
            'false_positive_probability' => $threatScore >= 80 ? 0.05 : rand(10, 50) / 100,
            'correlation_score' => rand(60, 95),
            'threat_indicators' => $this->getThreatIndicators($threatScore),
            'behavioral_analysis' => [
                'anomaly_score' => $threatScore >= 60 ? rand(70, 95) : rand(10, 40),
                'pattern_matches' => $threatScore >= 60 ? rand(3, 8) : rand(0, 2)
            ]
        ];
    }

    private function getThreatIndicators(float $threatScore): array
    {
        $indicators = [];
        
        if ($threatScore >= 80) {
            $indicators[] = 'known_malicious_ip';
            $indicators[] = 'suspicious_payload';
            $indicators[] = 'anomalous_behavior';
        } elseif ($threatScore >= 60) {
            $indicators[] = 'suspicious_payload';
            $indicators[] = 'anomalous_behavior';
        } elseif ($threatScore >= 40) {
            $indicators[] = 'unusual_pattern';
        }

        return $indicators;
    }

    private function getRandomUserId(): ?int
    {
        $user = User::inRandomOrder()->first();
        return $user ? $user->id : null;
    }

    private function generateSessionId(): string
    {
        return 'session_' . uniqid() . '_' . rand(1000, 9999);
    }

    private function getCountryName(string $code): string
    {
        $countries = [
            'US' => 'United States',
            'CN' => 'China',
            'RU' => 'Russia',
            'DE' => 'Germany',
            'GB' => 'United Kingdom',
            'FR' => 'France',
            'JP' => 'Japan',
            'BR' => 'Brazil'
        ];

        return $countries[$code] ?? 'Unknown';
    }
}
