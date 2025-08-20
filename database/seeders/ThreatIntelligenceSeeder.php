<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThreatIntelligence;
use Carbon\Carbon;

class ThreatIntelligenceSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🔍 Creando inteligencia de amenazas de prueba...');

        // IPs de ejemplo con diferentes niveles de amenaza
        $threatIPs = [
            '203.0.113.10' => [
                'threat_score' => 95,
                'classification' => 'critical',
                'threat_type' => 'malware_distribution',
                'malware_family' => 'Emotet',
                'country_code' => 'RU',
                'region' => 'Moscow',
                'city' => 'Moscow'
            ],
            '185.199.108.154' => [
                'threat_score' => 85,
                'classification' => 'high',
                'threat_type' => 'botnet_control',
                'malware_family' => 'Mirai',
                'country_code' => 'CN',
                'region' => 'Beijing',
                'city' => 'Beijing'
            ],
            '198.51.100.75' => [
                'threat_score' => 75,
                'classification' => 'high',
                'threat_type' => 'phishing_campaign',
                'malware_family' => 'PhishingKit',
                'country_code' => 'US',
                'region' => 'California',
                'city' => 'Los Angeles'
            ],
            '104.21.92.193' => [
                'threat_score' => 65,
                'classification' => 'medium',
                'threat_type' => 'scanning_activity',
                'malware_family' => null,
                'country_code' => 'DE',
                'region' => 'Berlin',
                'city' => 'Berlin'
            ],
            '45.33.12.200' => [
                'threat_score' => 45,
                'classification' => 'medium',
                'threat_type' => 'suspicious_behavior',
                'malware_family' => null,
                'country_code' => 'GB',
                'region' => 'England',
                'city' => 'London'
            ],
            '172.16.0.25' => [
                'threat_score' => 35,
                'classification' => 'low',
                'threat_type' => 'unusual_pattern',
                'malware_family' => null,
                'country_code' => 'FR',
                'region' => 'Île-de-France',
                'city' => 'Paris'
            ],
            '10.0.0.50' => [
                'threat_score' => 25,
                'classification' => 'low',
                'threat_type' => 'minor_anomaly',
                'malware_family' => null,
                'country_code' => 'JP',
                'region' => 'Tokyo',
                'city' => 'Tokyo'
            ],
            '192.168.1.100' => [
                'threat_score' => 15,
                'classification' => 'minimal',
                'threat_type' => 'normal_traffic',
                'malware_family' => null,
                'country_code' => 'BR',
                'region' => 'São Paulo',
                'city' => 'São Paulo'
            ]
        ];

        // Vectores de ataque comunes
        $attackVectors = [
            'malware_distribution' => ['email_attachment', 'drive_by_download', 'malicious_ads'],
            'botnet_control' => ['irc_command', 'http_command', 'dns_tunneling'],
            'phishing_campaign' => ['credential_harvesting', 'malware_delivery', 'social_engineering'],
            'scanning_activity' => ['port_scanning', 'vulnerability_scanning', 'service_enumeration'],
            'suspicious_behavior' => ['unusual_timing', 'anomalous_payload', 'suspicious_user_agent'],
            'unusual_pattern' => ['irregular_frequency', 'unexpected_requests', 'odd_geographic_pattern'],
            'minor_anomaly' => ['slight_deviation', 'unusual_headers', 'strange_parameters'],
            'normal_traffic' => ['standard_requests', 'expected_behavior', 'legitimate_activity']
        ];

        // Sectores objetivo
        $targetedSectors = [
            'malware_distribution' => ['financial', 'healthcare', 'government', 'education'],
            'botnet_control' => ['infrastructure', 'telecommunications', 'energy', 'transportation'],
            'phishing_campaign' => ['banking', 'ecommerce', 'social_media', 'email_providers'],
            'scanning_activity' => ['technology', 'hosting', 'cloud_services', 'data_centers'],
            'suspicious_behavior' => ['retail', 'manufacturing', 'logistics', 'consulting'],
            'unusual_pattern' => ['media', 'entertainment', 'gaming', 'streaming'],
            'minor_anomaly' => ['non_profit', 'research', 'academic', 'personal'],
            'normal_traffic' => ['general', 'public', 'commercial', 'residential']
        ];

        // Información de red
        $networkInfo = [
            '203.0.113.10' => ['asn' => 'AS12345', 'isp' => 'Malicious ISP', 'organization' => 'Threat Actor Group'],
            '185.199.108.154' => ['asn' => 'AS67890', 'isp' => 'Suspicious Network', 'organization' => 'Botnet Operator'],
            '198.51.100.75' => ['asn' => 'AS11111', 'isp' => 'Phishing Hosting', 'organization' => 'Cyber Criminal'],
            '104.21.92.193' => ['asn' => 'AS22222', 'isp' => 'Scanning Provider', 'organization' => 'Security Researcher'],
            '45.33.12.200' => ['asn' => 'AS33333', 'isp' => 'Regular ISP', 'organization' => 'Normal Company'],
            '172.16.0.25' => ['asn' => 'AS44444', 'isp' => 'Business Network', 'organization' => 'Corporate Entity'],
            '10.0.0.50' => ['asn' => 'AS55555', 'isp' => 'Local Provider', 'organization' => 'Small Business'],
            '192.168.1.100' => ['asn' => 'AS66666', 'isp' => 'Home Internet', 'organization' => 'Individual User']
        ];

        $threatsCreated = 0;
        $startDate = Carbon::now()->subDays(90);

        foreach ($threatIPs as $ip => $config) {
            $threatScore = $config['threat_score'];
            $classification = $config['classification'];
            $threatType = $config['threat_type'];
            $malwareFamily = $config['malware_family'];
            $countryCode = $config['country_code'];
            $region = $config['region'];
            $city = $config['city'];

            // Generar fechas aleatorias
            $firstSeen = $startDate->copy()->addDays(rand(0, 60));
            $lastSeen = $firstSeen->copy()->addDays(rand(1, 30));
            $lastUpdated = $lastSeen->copy()->addDays(rand(0, 7));

            // Determinar estado y verificación
            $status = $this->determineStatus($threatScore);
            $verified = $threatScore >= 70;
            $falsePositive = $threatScore <= 30 && rand(1, 10) === 1;

            // Crear amenaza
            ThreatIntelligence::create([
                'ip_address' => $ip,
                'threat_score' => $threatScore,
                'classification' => $classification,
                'confidence' => $this->calculateConfidence($threatScore),
                'data' => $this->generateThreatData($threatScore, $threatType),
                'sources' => $this->generateSources($threatScore),
                'last_updated' => $lastUpdated,
                'first_seen' => $firstSeen,
                'last_seen' => $lastSeen,
                'threat_type' => $threatType,
                'malware_family' => $malwareFamily,
                'attack_vectors' => $attackVectors[$threatType] ?? [],
                'targeted_sectors' => $targetedSectors[$threatType] ?? [],
                'geographic_origin' => "{$city}, {$region}, {$countryCode}",
                'asn' => $networkInfo[$ip]['asn'],
                'isp' => $networkInfo[$ip]['isp'],
                'organization' => $networkInfo[$ip]['organization'],
                'country_code' => $countryCode,
                'region' => $region,
                'city' => $city,
                'latitude' => $this->getLatitude($countryCode),
                'longitude' => $this->getLongitude($countryCode),
                'timezone' => $this->getTimezone($countryCode),
                'status' => $status,
                'verified' => $verified,
                'false_positive' => $falsePositive,
                'notes' => $this->generateThreatNotes($threatScore, $threatType, $malwareFamily),
                'metadata' => $this->generateThreatMetadata($threatScore, $threatType),
                'created_at' => $firstSeen,
                'updated_at' => $lastUpdated
            ]);

            $threatsCreated++;
        }

        $this->command->info("✅ {$threatsCreated} amenazas de inteligencia creadas exitosamente");
    }

    private function determineStatus(float $threatScore): string
    {
        if ($threatScore >= 80) return 'active';
        if ($threatScore >= 60) return 'monitoring';
        if ($threatScore >= 40) return 'investigating';
        return 'inactive';
    }

    private function calculateConfidence(float $threatScore): float
    {
        if ($threatScore >= 80) return rand(85, 95);
        if ($threatScore >= 60) return rand(70, 85);
        if ($threatScore >= 40) return rand(60, 75);
        return rand(50, 65);
    }

    private function generateThreatData(float $threatScore, string $threatType): array
    {
        $data = [
            'threat_score' => $threatScore,
            'threat_type' => $threatType,
            'detection_method' => $threatScore >= 70 ? 'ml_algorithm' : 'rule_based',
            'false_positive_probability' => $threatScore >= 80 ? 0.02 : rand(5, 30) / 100,
            'correlation_score' => rand(60, 95),
            'threat_indicators' => $this->getThreatIndicators($threatScore, $threatType)
        ];

        if ($threatScore >= 70) {
            $data['ml_confidence'] = rand(80, 95);
            $data['behavioral_analysis'] = [
                'anomaly_score' => rand(75, 95),
                'pattern_matches' => rand(5, 10),
                'risk_factors' => $this->getRiskFactors($threatType)
            ];
        }

        return $data;
    }

    private function generateSources(float $threatScore): array
    {
        $sources = [];
        
        if ($threatScore >= 80) {
            $sources[] = 'abuseipdb';
            $sources[] = 'virustotal';
            $sources[] = 'alienvault';
            $sources[] = 'threatfox';
        } elseif ($threatScore >= 60) {
            $sources[] = 'abuseipdb';
            $sources[] = 'virustotal';
            $sources[] = 'alienvault';
        } elseif ($threatScore >= 40) {
            $sources[] = 'abuseipdb';
            $sources[] = 'virustotal';
        } else {
            $sources[] = 'abuseipdb';
        }

        return $sources;
    }

    private function getThreatIndicators(float $threatScore, string $threatType): array
    {
        $indicators = [];
        
        if ($threatScore >= 80) {
            $indicators[] = 'known_malicious_ip';
            $indicators[] = 'malware_communication';
            $indicators[] = 'command_control_traffic';
            $indicators[] = 'anomalous_behavior';
        } elseif ($threatScore >= 60) {
            $indicators[] = 'suspicious_activity';
            $indicators[] = 'unusual_patterns';
            $indicators[] = 'potential_threat';
        } elseif ($threatScore >= 40) {
            $indicators[] = 'scanning_behavior';
            $indicators[] = 'unusual_requests';
        }

        // Agregar indicadores específicos del tipo de amenaza
        switch ($threatType) {
            case 'malware_distribution':
                $indicators[] = 'malware_hosting';
                $indicators[] = 'phishing_landing_page';
                break;
            case 'botnet_control':
                $indicators[] = 'botnet_command';
                $indicators[] = 'ddos_activity';
                break;
            case 'phishing_campaign':
                $indicators[] = 'credential_harvesting';
                $indicators[] = 'social_engineering';
                break;
        }

        return $indicators;
    }

    private function getRiskFactors(string $threatType): array
    {
        $riskFactors = [
            'malware_distribution' => ['high_volume', 'multiple_targets', 'persistent_activity'],
            'botnet_control' => ['command_frequency', 'bot_count', 'attack_capability'],
            'phishing_campaign' => ['target_breadth', 'success_rate', 'credential_value'],
            'scanning_activity' => ['scan_intensity', 'vulnerability_focus', 'target_selection'],
            'suspicious_behavior' => ['pattern_deviation', 'timing_anomaly', 'payload_suspicion'],
            'unusual_pattern' => ['frequency_change', 'request_anomaly', 'geographic_shift'],
            'minor_anomaly' => ['slight_deviation', 'unusual_parameter', 'strange_header'],
            'normal_traffic' => ['expected_behavior', 'standard_pattern', 'legitimate_activity']
        ];

        return $riskFactors[$threatType] ?? ['unknown_risk'];
    }

    private function generateThreatNotes(float $threatScore, string $threatType, ?string $malwareFamily): string
    {
        $notes = [
            'malware_distribution' => "IP associated with {$malwareFamily} malware distribution. Multiple victims reported.",
            'botnet_control' => "Command and control server for {$malwareFamily} botnet. High volume of bot communications.",
            'phishing_campaign' => "Hosting phishing pages targeting financial and e-commerce users.",
            'scanning_activity' => "Aggressive scanning behavior detected. Multiple ports and services targeted.",
            'suspicious_behavior' => "Unusual traffic patterns and suspicious payloads detected.",
            'unusual_pattern' => "Minor deviations from expected behavior patterns.",
            'minor_anomaly' => "Slight anomalies in traffic, requires monitoring.",
            'normal_traffic' => "Traffic appears normal, no immediate threats detected."
        ];

        $baseNote = $notes[$threatType] ?? 'Threat intelligence data available.';
        
        if ($threatScore >= 80) {
            $baseNote .= " CRITICAL: Immediate action required.";
        } elseif ($threatScore >= 60) {
            $baseNote .= " HIGH: Enhanced monitoring recommended.";
        } elseif ($threatScore >= 40) {
            $baseNote .= " MEDIUM: Continue monitoring.";
        }

        return $baseNote;
    }

    private function generateThreatMetadata(float $threatScore): array
    {
        return [
            'detection_engine' => $threatScore >= 70 ? 'advanced_ml' : 'rule_based',
            'update_frequency' => $threatScore >= 80 ? 'real_time' : 'hourly',
            'data_quality' => $threatScore >= 70 ? 'high' : 'medium',
            'correlation_confidence' => rand(60, 95),
            'threat_evolution' => [
                'trend' => $threatScore >= 70 ? 'increasing' : 'stable',
                'velocity' => $threatScore >= 80 ? 'high' : 'low',
                'spread' => $threatScore >= 70 ? 'global' : 'regional'
            ],
            'mitigation_status' => $threatScore >= 80 ? 'active' : 'monitoring'
        ];
    }

    private function getLatitude(string $countryCode): float
    {
        $latitudes = [
            'US' => 39.8283, 'CN' => 35.8617, 'RU' => 61.5240, 'DE' => 51.1657,
            'GB' => 55.3781, 'FR' => 46.2276, 'JP' => 36.2048, 'BR' => -14.2350
        ];

        return $latitudes[$countryCode] ?? 0.0;
    }

    private function getLongitude(string $countryCode): float
    {
        $longitudes = [
            'US' => -98.5795, 'CN' => 104.1954, 'RU' => 105.3188, 'DE' => 10.4515,
            'GB' => -3.4360, 'FR' => 2.2137, 'JP' => 138.2529, 'BR' => -51.9253
        ];

        return $longitudes[$countryCode] ?? 0.0;
    }

    private function getTimezone(string $countryCode): string
    {
        $timezones = [
            'US' => 'America/New_York', 'CN' => 'Asia/Shanghai', 'RU' => 'Europe/Moscow',
            'DE' => 'Europe/Berlin', 'GB' => 'Europe/London', 'FR' => 'Europe/Paris',
            'JP' => 'Asia/Tokyo', 'BR' => 'America/Sao_Paulo'
        ];

        return $timezones[$countryCode] ?? 'UTC';
    }
}
