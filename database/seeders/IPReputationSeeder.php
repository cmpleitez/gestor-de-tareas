<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IPReputation;
use Carbon\Carbon;

class IPReputationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🌍 Creando reputaciones de IP de prueba...');

        // IPs de ejemplo con diferentes niveles de reputación
        $reputationIPs = [
            '203.0.113.10' => [
                'reputation_score' => 95,
                'risk_level' => 'critical',
                'total_requests' => 1500,
                'threat_requests' => 1200,
                'whitelisted' => false,
                'blacklisted' => true,
                'country_code' => 'RU',
                'region' => 'Moscow',
                'city' => 'Moscow'
            ],
            '185.199.108.154' => [
                'reputation_score' => 85,
                'risk_level' => 'high',
                'total_requests' => 800,
                'threat_requests' => 600,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'CN',
                'region' => 'Beijing',
                'city' => 'Beijing'
            ],
            '198.51.100.75' => [
                'reputation_score' => 75,
                'risk_level' => 'high',
                'total_requests' => 600,
                'threat_requests' => 400,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'US',
                'region' => 'California',
                'city' => 'Los Angeles'
            ],
            '104.21.92.193' => [
                'reputation_score' => 65,
                'risk_level' => 'medium',
                'total_requests' => 400,
                'threat_requests' => 200,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'DE',
                'region' => 'Berlin',
                'city' => 'Berlin'
            ],
            '45.33.12.200' => [
                'reputation_score' => 45,
                'risk_level' => 'medium',
                'total_requests' => 300,
                'threat_requests' => 100,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'GB',
                'region' => 'England',
                'city' => 'London'
            ],
            '172.16.0.25' => [
                'reputation_score' => 35,
                'risk_level' => 'low',
                'total_requests' => 200,
                'threat_requests' => 50,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'FR',
                'region' => 'Île-de-France',
                'city' => 'Paris'
            ],
            '10.0.0.50' => [
                'reputation_score' => 25,
                'risk_level' => 'low',
                'total_requests' => 150,
                'threat_requests' => 30,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'JP',
                'region' => 'Tokyo',
                'city' => 'Tokyo'
            ],
            '192.168.1.100' => [
                'reputation_score' => 15,
                'risk_level' => 'minimal',
                'total_requests' => 100,
                'threat_requests' => 10,
                'whitelisted' => false,
                'blacklisted' => false,
                'country_code' => 'BR',
                'region' => 'São Paulo',
                'city' => 'São Paulo'
            ],
            '127.0.0.1' => [
                'reputation_score' => 0,
                'risk_level' => 'minimal',
                'total_requests' => 50,
                'threat_requests' => 0,
                'whitelisted' => true,
                'blacklisted' => false,
                'country_code' => 'LOCAL',
                'region' => 'Local',
                'city' => 'Localhost'
            ]
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
            '192.168.1.100' => ['asn' => 'AS66666', 'isp' => 'Home Internet', 'organization' => 'Individual User'],
            '127.0.0.1' => ['asn' => 'AS00000', 'isp' => 'Local Network', 'organization' => 'Local System']
        ];

        $reputationsCreated = 0;
        $startDate = Carbon::now()->subDays(90);

        foreach ($reputationIPs as $ip => $config) {
            $reputationScore = $config['reputation_score'];
            $riskLevel = $config['risk_level'];
            $totalRequests = $config['total_requests'];
            $threatRequests = $config['threat_requests'];
            $whitelisted = $config['whitelisted'];
            $blacklisted = $config['blacklisted'];
            $countryCode = $config['country_code'];
            $region = $config['region'];
            $city = $config['city'];

            // Calcular requests benignos
            $benignRequests = $totalRequests - $threatRequests;
            
            // Calcular frecuencia de requests
            $requestFrequency = $this->calculateRequestFrequency($totalRequests, $startDate);

            // Generar fechas aleatorias
            $firstSeen = $startDate->copy()->addDays(rand(0, 60));
            $lastSeen = $firstSeen->copy()->addDays(rand(1, 30));
            $lastUpdated = $lastSeen->copy()->addDays(rand(0, 7));

            // Crear reputación
            IPReputation::create([
                'ip_address' => $ip,
                'reputation_score' => $reputationScore,
                'risk_level' => $riskLevel,
                'confidence' => $this->calculateConfidence($reputationScore),
                'data' => $this->generateReputationData($reputationScore, $ip),
                'last_updated' => $lastUpdated,
                'first_seen' => $firstSeen,
                'last_seen' => $lastSeen,
                'total_requests' => $totalRequests,
                'threat_requests' => $threatRequests,
                'benign_requests' => $benignRequests,
                'request_frequency' => $requestFrequency,
                'geographic_data' => $this->generateGeographicData($countryCode, $region, $city),
                'network_data' => $this->generateNetworkData($networkInfo[$ip]),
                'behavioral_patterns' => $this->generateBehavioralPatterns($reputationScore),
                'threat_indicators' => $this->generateThreatIndicators($reputationScore),
                'whitelisted' => $whitelisted,
                'blacklisted' => $blacklisted,
                'notes' => $this->generateReputationNotes($reputationScore, $riskLevel, $ip),
                'metadata' => $this->generateReputationMetadata($reputationScore, $riskLevel),
                'created_at' => $firstSeen,
                'updated_at' => $lastUpdated
            ]);

            $reputationsCreated++;
        }

        $this->command->info("✅ {$reputationsCreated} reputaciones de IP creadas exitosamente");
    }

    private function calculateRequestFrequency(int $totalRequests, Carbon $startDate): float
    {
        $daysSinceStart = max(1, $startDate->diffInDays(now()));
        return round($totalRequests / $daysSinceStart, 2);
    }

    private function calculateConfidence(float $reputationScore): float
    {
        if ($reputationScore >= 80) return rand(85, 95);
        if ($reputationScore >= 60) return rand(70, 85);
        if ($reputationScore >= 40) return rand(60, 75);
        return rand(50, 65);
    }

    private function generateReputationData(float $reputationScore, string $ip): array
    {
        $data = [
            'reputation_score' => $reputationScore,
            'ip_address' => $ip,
            'analysis_method' => $reputationScore >= 60 ? 'ml_enhanced' : 'rule_based',
            'false_positive_probability' => $reputationScore >= 80 ? 0.03 : rand(5, 35) / 100,
            'correlation_score' => rand(60, 95),
            'reputation_factors' => $this->getReputationFactors($reputationScore)
        ];

        if ($reputationScore >= 60) {
            $data['ml_confidence'] = rand(75, 95);
            $data['behavioral_analysis'] = [
                'anomaly_score' => rand(70, 95),
                'pattern_matches' => rand(4, 9),
                'risk_factors' => $this->getRiskFactors($reputationScore)
            ];
        }

        return $data;
    }

    private function generateGeographicData(string $countryCode, string $region, string $city): array
    {
        $data = [
            'country_code' => $countryCode,
            'country_name' => $this->getCountryName($countryCode),
            'region' => $region,
            'city' => $city,
            'latitude' => $this->getLatitude($countryCode),
            'longitude' => $this->getLongitude($countryCode),
            'timezone' => $this->getTimezone($countryCode)
        ];

        if ($countryCode === 'LOCAL') {
            $data['latitude'] = 0.0;
            $data['longitude'] = 0.0;
            $data['timezone'] = 'UTC';
        }

        return $data;
    }

    private function generateNetworkData(array $networkInfo): array
    {
        return [
            'asn' => $networkInfo['asn'],
            'isp' => $networkInfo['isp'],
            'organization' => $networkInfo['organization'],
            'network_type' => $this->determineNetworkType($networkInfo['asn']),
            'connection_speed' => $this->getConnectionSpeed($networkInfo['asn']),
            'routing_info' => [
                'bgp_prefix' => $this->generateBGPPrefix($networkInfo['asn']),
                'routing_policy' => $this->getRoutingPolicy($networkInfo['asn'])
            ]
        ];
    }

    private function generateBehavioralPatterns(float $reputationScore): array
    {
        $patterns = [
            'request_timing' => $this->generateRequestTiming($reputationScore),
            'payload_patterns' => $this->generatePayloadPatterns($reputationScore),
            'user_agent_patterns' => $this->generateUserAgentPatterns($reputationScore),
            'geographic_movement' => $this->generateGeographicMovement($reputationScore),
            'resource_targeting' => $this->generateResourceTargeting($reputationScore)
        ];

        if ($reputationScore >= 60) {
            $patterns['anomalous_behavior'] = [
                'unusual_frequency' => rand(70, 95),
                'suspicious_patterns' => rand(5, 10),
                'risk_indicators' => $this->getRiskIndicators($reputationScore)
            ];
        }

        return $patterns;
    }

    private function generateThreatIndicators(float $reputationScore): array
    {
        $indicators = [];
        
        if ($reputationScore >= 80) {
            $indicators[] = 'known_malicious_ip';
            $indicators[] = 'malware_communication';
            $indicators[] = 'command_control_traffic';
            $indicators[] = 'anomalous_behavior';
            $indicators[] = 'high_threat_volume';
        } elseif ($reputationScore >= 60) {
            $indicators[] = 'suspicious_activity';
            $indicators[] = 'unusual_patterns';
            $indicators[] = 'potential_threat';
            $indicators[] = 'moderate_threat_volume';
        } elseif ($reputationScore >= 40) {
            $indicators[] = 'scanning_behavior';
            $indicators[] = 'unusual_requests';
            $indicators[] = 'low_threat_volume';
        }

        return $indicators;
    }

    private function generateReputationNotes(float $reputationScore, string $riskLevel, string $ip): string
    {
        $baseNotes = [
            'critical' => "IP {$ip} has critical reputation score. Multiple high-threat indicators detected.",
            'high' => "IP {$ip} shows high-risk behavior. Enhanced monitoring recommended.",
            'medium' => "IP {$ip} exhibits medium-risk patterns. Continue monitoring.",
            'low' => "IP {$ip} shows low-risk behavior. Standard monitoring sufficient.",
            'minimal' => "IP {$ip} has minimal risk. Normal traffic patterns observed."
        ];

        $baseNote = $baseNotes[$riskLevel] ?? "IP {$ip} reputation analyzed.";

        if ($reputationScore >= 80) {
            $baseNote .= " CRITICAL: Immediate action required.";
        } elseif ($reputationScore >= 60) {
            $baseNote .= " HIGH: Enhanced monitoring recommended.";
        } elseif ($reputationScore >= 40) {
            $baseNote .= " MEDIUM: Continue monitoring.";
        }

        return $baseNote;
    }

    private function generateReputationMetadata(float $reputationScore, string $riskLevel): array
    {
        return [
            'analysis_engine' => $reputationScore >= 60 ? 'advanced_ml' : 'rule_based',
            'update_frequency' => $reputationScore >= 80 ? 'real_time' : 'hourly',
            'data_quality' => $reputationScore >= 70 ? 'high' : 'medium',
            'correlation_confidence' => rand(60, 95),
            'reputation_evolution' => [
                'trend' => $reputationScore >= 70 ? 'increasing' : 'stable',
                'velocity' => $reputationScore >= 80 ? 'high' : 'low',
                'spread' => $reputationScore >= 70 ? 'global' : 'regional'
            ],
            'mitigation_status' => $reputationScore >= 80 ? 'active' : 'monitoring'
        ];
    }

    private function getReputationFactors(float $reputationScore): array
    {
        $factors = [];
        
        if ($reputationScore >= 80) {
            $factors[] = 'high_threat_volume';
            $factors[] = 'malicious_behavior';
            $factors[] = 'known_bad_actor';
            $factors[] = 'anomalous_patterns';
        } elseif ($reputationScore >= 60) {
            $factors[] = 'suspicious_activity';
            $factors[] = 'unusual_patterns';
            $factors[] = 'potential_threat';
        } elseif ($reputationScore >= 40) {
            $factors[] = 'scanning_behavior';
            $factors[] = 'unusual_requests';
        }

        return $factors;
    }

    private function getRiskFactors(float $reputationScore): array
    {
        $riskFactors = [
            'high_threat_volume' => ['threat_frequency', 'attack_intensity', 'target_breadth'],
            'malicious_behavior' => ['malware_activity', 'phishing_attempts', 'scanning_behavior'],
            'known_bad_actor' => ['reputation_history', 'threat_association', 'malware_family'],
            'anomalous_patterns' => ['timing_anomaly', 'payload_suspicion', 'geographic_shift']
        ];

        if ($reputationScore >= 80) {
            return $riskFactors['high_threat_volume'] + $riskFactors['malicious_behavior'];
        } elseif ($reputationScore >= 60) {
            return $riskFactors['suspicious_activity'] ?? ['unusual_patterns'];
        }

        return ['low_risk'];
    }

    private function getRiskIndicators(float $reputationScore): array
    {
        $indicators = [];
        
        if ($reputationScore >= 80) {
            $indicators[] = 'high_frequency_attacks';
            $indicators[] = 'malware_distribution';
            $indicators[] = 'command_control';
        } elseif ($reputationScore >= 60) {
            $indicators[] = 'suspicious_timing';
            $indicators[] = 'unusual_payloads';
            $indicators[] = 'scanning_behavior';
        }

        return $indicators;
    }

    private function generateRequestTiming(float $reputationScore): array
    {
        $timing = [
            'hourly_distribution' => array_fill(0, 24, rand(1, 10)),
            'daily_distribution' => array_fill(0, 7, rand(5, 20)),
            'frequency_pattern' => $reputationScore >= 60 ? 'irregular' : 'regular'
        ];

        if ($reputationScore >= 60) {
            $timing['anomaly_score'] = rand(70, 95);
        }

        return $timing;
    }

    private function generatePayloadPatterns(float $reputationScore): array
    {
        $patterns = [
            'size_distribution' => [
                'small' => rand(20, 40),
                'medium' => rand(30, 50),
                'large' => rand(10, 30)
            ],
            'content_types' => ['text/html', 'application/json', 'text/plain'],
            'suspicious_patterns' => $reputationScore >= 60 ? rand(2, 6) : 0
        ];

        return $patterns;
    }

    private function generateUserAgentPatterns(float $reputationScore): array
    {
        $patterns = [
            'browser_diversity' => rand(3, 8),
            'bot_indicators' => $reputationScore >= 60 ? rand(2, 5) : 0,
            'suspicious_agents' => $reputationScore >= 60 ? rand(1, 3) : 0
        ];

        return $patterns;
    }

    private function generateGeographicMovement(float $reputationScore): array
    {
        $movement = [
            'countries_visited' => $reputationScore >= 60 ? rand(3, 8) : rand(1, 3),
            'geographic_anomaly' => $reputationScore >= 60 ? rand(70, 95) : rand(10, 40),
            'travel_pattern' => $reputationScore >= 60 ? 'suspicious' : 'normal'
        ];

        return $movement;
    }

    private function generateResourceTargeting(float $reputationScore): array
    {
        $targeting = [
            'endpoints_targeted' => rand(5, 15),
            'suspicious_paths' => $reputationScore >= 60 ? rand(2, 6) : 0,
            'targeting_pattern' => $reputationScore >= 60 ? 'aggressive' : 'normal'
        ];

        return $targeting;
    }

    private function determineNetworkType(string $asn): string
    {
        if (str_contains($asn, 'AS00000')) return 'local';
        if (str_contains($asn, 'AS66666')) return 'residential';
        if (str_contains($asn, 'AS55555')) return 'business';
        if (str_contains($asn, 'AS44444')) return 'corporate';
        if (str_contains($asn, 'AS33333')) return 'isp';
        if (str_contains($asn, 'AS22222')) return 'hosting';
        if (str_contains($asn, 'AS11111')) return 'malicious';
        if (str_contains($asn, 'AS67890')) return 'suspicious';
        if (str_contains($asn, 'AS12345')) return 'malicious';
        
        return 'unknown';
    }

    private function getConnectionSpeed(string $asn): string
    {
        if (str_contains($asn, 'AS00000')) return 'local';
        if (str_contains($asn, 'AS66666')) return 'residential';
        if (str_contains($asn, 'AS55555')) return 'business';
        if (str_contains($asn, 'AS44444')) return 'corporate';
        if (str_contains($asn, 'AS33333')) return 'high';
        if (str_contains($asn, 'AS22222')) return 'very_high';
        if (str_contains($asn, 'AS11111')) return 'variable';
        if (str_contains($asn, 'AS67890')) return 'variable';
        if (str_contains($asn, 'AS12345')) return 'variable';
        
        return 'unknown';
    }

    private function generateBGPPrefix(string $asn): string
    {
        $prefixes = [
            'AS12345' => '203.0.113.0/24',
            'AS67890' => '185.199.108.0/24',
            'AS11111' => '198.51.100.0/24',
            'AS22222' => '104.21.92.0/24',
            'AS33333' => '45.33.12.0/24',
            'AS44444' => '172.16.0.0/16',
            'AS55555' => '10.0.0.0/8',
            'AS66666' => '192.168.1.0/24',
            'AS00000' => '127.0.0.0/8'
        ];

        return $prefixes[$asn] ?? '0.0.0.0/0';
    }

    private function getRoutingPolicy(string $asn): string
    {
        if (str_contains($asn, 'AS00000')) return 'local_only';
        if (str_contains($asn, 'AS66666')) return 'residential';
        if (str_contains($asn, 'AS55555')) return 'business';
        if (str_contains($asn, 'AS44444')) return 'corporate';
        if (str_contains($asn, 'AS33333')) return 'transit';
        if (str_contains($asn, 'AS22222')) return 'hosting';
        if (str_contains($asn, 'AS11111')) return 'malicious';
        if (str_contains($asn, 'AS67890')) return 'suspicious';
        if (str_contains($asn, 'AS12345')) return 'malicious';
        
        return 'unknown';
    }

    private function getCountryName(string $code): string
    {
        $countries = [
            'US' => 'United States', 'CN' => 'China', 'RU' => 'Russia', 'DE' => 'Germany',
            'GB' => 'United Kingdom', 'FR' => 'France', 'JP' => 'Japan', 'BR' => 'Brazil',
            'LOCAL' => 'Local Network'
        ];

        return $countries[$code] ?? 'Unknown';
    }

    private function getLatitude(string $countryCode): float
    {
        $latitudes = [
            'US' => 39.8283, 'CN' => 35.8617, 'RU' => 61.5240, 'DE' => 51.1657,
            'GB' => 55.3781, 'FR' => 46.2276, 'JP' => 36.2048, 'BR' => -14.2350,
            'LOCAL' => 0.0
        ];

        return $latitudes[$countryCode] ?? 0.0;
    }

    private function getLongitude(string $countryCode): float
    {
        $longitudes = [
            'US' => -98.5795, 'CN' => 104.1954, 'RU' => 105.3188, 'DE' => 10.4515,
            'GB' => -3.4360, 'FR' => 2.2137, 'JP' => 138.2529, 'BR' => -51.9253,
            'LOCAL' => 0.0
        ];

        return $longitudes[$countryCode] ?? 0.0;
    }

    private function getTimezone(string $countryCode): string
    {
        $timezones = [
            'US' => 'America/New_York', 'CN' => 'Asia/Shanghai', 'RU' => 'Europe/Moscow',
            'DE' => 'Europe/Berlin', 'GB' => 'Europe/London', 'FR' => 'Europe/Paris',
            'JP' => 'Asia/Tokyo', 'BR' => 'America/Sao_Paulo', 'LOCAL' => 'UTC'
        ];

        return $timezones[$countryCode] ?? 'UTC';
    }
}
