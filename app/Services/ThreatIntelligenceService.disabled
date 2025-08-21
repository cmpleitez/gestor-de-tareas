<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence;
use App\Models\IPReputation;
use App\Services\MachineLearning\ThreatCorrelationEngine;
use App\Services\MachineLearning\ReputationScoring;

class ThreatIntelligenceService
{
    protected $threatCorrelationEngine;
    protected $reputationScoring;
    protected $config;

    public function __construct(
        ThreatCorrelationEngine $threatCorrelationEngine,
        ReputationScoring $reputationScoring
    ) {
        $this->threatCorrelationEngine = $threatCorrelationEngine;
        $this->reputationScoring = $reputationScoring;
        
        // Configuración de APIs de inteligencia de amenazas
        $this->config = [
            'apis' => [
                'abuseipdb' => [
                    'url' => 'https://api.abuseipdb.com/api/v2/check',
                    'key' => env('ABUSEIPDB_API_KEY'),
                    'timeout' => 10
                ],
                'virustotal' => [
                    'url' => 'https://www.virustotal.com/vtapi/v2/ip-address/report',
                    'key' => env('VIRUSTOTAL_API_KEY'),
                    'timeout' => 15
                ],
                'ipqualityscore' => [
                    'url' => 'https://ipqualityscore.com/api/json/ip',
                    'key' => env('IPQUALITYSCORE_API_KEY'),
                    'timeout' => 8
                ],
                'ipapi' => [
                    'url' => 'http://ip-api.com/json',
                    'key' => null,
                    'timeout' => 5
                ]
            ],
            'cache_duration' => [
                'reputation' => 3600, // 1 hora
                'geolocation' => 86400, // 24 horas
                'threat_data' => 1800 // 30 minutos
            ],
            'scoring_weights' => [
                'abuseipdb' => 0.35,
                'virustotal' => 0.30,
                'ipqualityscore' => 0.25,
                'ipapi' => 0.10
            ]
        ];
    }

    /**
     * Análisis completo de inteligencia de amenazas para una IP
     */
    public function analyzeThreatIntelligence(string $ip): array
    {
        try {
            // Verificar cache primero
            $cacheKey = "threat_intelligence_{$ip}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Análisis paralelo de múltiples fuentes
            $results = $this->parallelThreatAnalysis($ip);
            
            // Correlación de amenazas usando ML
            $correlationScore = $this->threatCorrelationEngine->correlateThreats($results);
            
            // Análisis de reputación histórica
            $historicalAnalysis = $this->analyzeHistoricalReputation($ip);
            
            // Análisis de comportamiento de amenazas
            $behavioralAnalysis = $this->analyzeThreatBehavior($ip);
            
            // Score de amenaza final
            $finalThreatScore = $this->calculateFinalThreatScore($results, $correlationScore, $historicalAnalysis, $behavioralAnalysis);
            
            // Clasificación de amenaza
            $threatClassification = $this->classifyThreat($finalThreatScore, $results);
            
            // Resultado final
            $finalResult = [
                'ip' => $ip,
                'threat_score' => $finalThreatScore,
                'classification' => $threatClassification,
                'confidence' => $this->calculateConfidence($results),
                'sources' => $results,
                'correlation_score' => $correlationScore,
                'historical_analysis' => $historicalAnalysis,
                'behavioral_analysis' => $behavioralAnalysis,
                'recommendations' => $this->generateRecommendations($finalThreatScore, $results),
                'last_updated' => now()->toISOString()
            ];
            
            // Guardar en cache
            Cache::put($cacheKey, $finalResult, now()->addSeconds($this->config['cache_duration']['threat_data']));
            
            // Guardar en base de datos
            $this->saveThreatIntelligence($ip, $finalResult);
            
            return $finalResult;
            
        } catch (\Exception $e) {
            Log::error('Threat Intelligence Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            // Fallback a análisis básico
            return $this->fallbackThreatAnalysis($ip);
        }
    }

    /**
     * Análisis paralelo de múltiples fuentes de amenazas
     */
    protected function parallelThreatAnalysis(string $ip): array
    {
        $results = [];
        $promises = [];
        
        foreach ($this->config['apis'] as $provider => $config) {
            $promises[$provider] = $this->asyncThreatCheck($ip, $provider, $config);
        }
        
        // Ejecutar todas las consultas en paralelo
        foreach ($promises as $provider => $promise) {
            try {
                $results[$provider] = $promise->wait();
            } catch (\Exception $e) {
                Log::warning("Failed to get threat data from {$provider}", [
                    'error' => $e->getMessage(),
                    'ip' => $ip
                ]);
                
                $results[$provider] = $this->getDefaultProviderResult($provider);
            }
        }
        
        return $results;
    }

    /**
     * Consulta asíncrona a proveedor de amenazas
     */
    protected function asyncThreatCheck(string $ip, string $provider, array $config)
    {
        return async(function() use ($ip, $provider, $config) {
            switch ($provider) {
                case 'abuseipdb':
                    return $this->queryAbuseIPDB($ip, $config);
                    
                case 'virustotal':
                    return $this->queryVirusTotal($ip, $config);
                    
                case 'ipqualityscore':
                    return $this->queryIPQualityScore($ip, $config);
                    
                case 'ipapi':
                    return $this->queryIPAPI($ip, $config);
                    
                default:
                    return $this->getDefaultProviderResult($provider);
            }
        });
    }

    /**
     * Consulta a AbuseIPDB
     */
    protected function queryAbuseIPDB(string $ip, array $config): array
    {
        try {
            $response = Http::timeout($config['timeout'])
                ->withHeaders([
                    'Key' => $config['key'],
                    'Accept' => 'application/json'
                ])
                ->get($config['url'], [
                    'ipAddress' => $ip,
                    'maxAgeInDays' => 90
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'abuse_confidence' => $data['data']['abuseConfidenceScore'] ?? 0,
                    'country_code' => $data['data']['countryCode'] ?? null,
                    'usage_type' => $data['data']['usageType'] ?? null,
                    'isp' => $data['data']['isp'] ?? null,
                    'domain' => $data['data']['domain'] ?? null,
                    'total_reports' => $data['data']['totalReports'] ?? 0,
                    'last_reported' => $data['data']['lastReportedAt'] ?? null,
                    'score' => $this->normalizeAbuseIPDBScore($data['data']['abuseConfidenceScore'] ?? 0)
                ];
            }
            
            return $this->getDefaultProviderResult('abuseipdb');
            
        } catch (\Exception $e) {
            Log::error('AbuseIPDB API Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return $this->getDefaultProviderResult('abuseipdb');
        }
    }

    /**
     * Consulta a VirusTotal
     */
    protected function queryVirusTotal(string $ip, array $config): array
    {
        try {
            $response = Http::timeout($config['timeout'])
                ->get($config['url'], [
                    'apikey' => $config['key'],
                    'ip' => $ip
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'detected_urls' => $data['detected_urls'] ?? 0,
                    'detected_communicating_samples' => $data['detected_communicating_samples'] ?? 0,
                    'detected_downloaded_samples' => $data['detected_downloaded_samples'] ?? 0,
                    'country' => $data['country'] ?? null,
                    'as_owner' => $data['as_owner'] ?? null,
                    'score' => $this->normalizeVirusTotalScore($data)
                ];
            }
            
            return $this->getDefaultProviderResult('virustotal');
            
        } catch (\Exception $e) {
            Log::error('VirusTotal API Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return $this->getDefaultProviderResult('virustotal');
        }
    }

    /**
     * Consulta a IPQualityScore
     */
    protected function queryIPQualityScore(string $ip, array $config): array
    {
        try {
            $response = Http::timeout($config['timeout'])
                ->get($config['url'], [
                    'key' => $config['key'],
                    'ip' => $ip,
                    'strictness' => 1
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'proxy' => $data['proxy'] ?? false,
                    'vpn' => $data['vpn'] ?? false,
                    'tor' => $data['tor'] ?? false,
                    'bot' => $data['bot'] ?? false,
                    'fraud_score' => $data['fraud_score'] ?? 0,
                    'country_code' => $data['country_code'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'score' => $this->normalizeIPQualityScore($data)
                ];
            }
            
            return $this->getDefaultProviderResult('ipqualityscore');
            
        } catch (\Exception $e) {
            Log::error('IPQualityScore API Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return $this->getDefaultProviderResult('ipqualityscore');
        }
    }

    /**
     * Consulta a IP-API (gratuita)
     */
    protected function queryIPAPI(string $ip, array $config): array
    {
        try {
            $response = Http::timeout($config['timeout'])
                ->get($config['url'] . '/' . $ip);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'country' => $data['country'] ?? null,
                    'country_code' => $data['countryCode'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'lat' => $data['lat'] ?? null,
                    'lon' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['isp'] ?? null,
                    'org' => $data['org'] ?? null,
                    'as' => $data['as'] ?? null,
                    'score' => $this->normalizeIPAPIScore($data)
                ];
            }
            
            return $this->getDefaultProviderResult('ipapi');
            
        } catch (\Exception $e) {
            Log::error('IP-API Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return $this->getDefaultProviderResult('ipapi');
        }
    }

    /**
     * Análisis de reputación histórica
     */
    protected function analyzeHistoricalReputation(string $ip): array
    {
        try {
            // Obtener eventos históricos de seguridad
            $securityEvents = SecurityEvent::where('ip_address', $ip)
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();
            
            if ($securityEvents->isEmpty()) {
                return [
                    'total_events' => 0,
                    'threat_events' => 0,
                    'last_threat' => null,
                    'threat_frequency' => 0,
                    'score' => 0
                ];
            }
            
            $threatEvents = $securityEvents->where('threat_score', '>=', 60);
            $totalEvents = $securityEvents->count();
            $threatCount = $threatEvents->count();
            
            return [
                'total_events' => $totalEvents,
                'threat_events' => $threatCount,
                'last_threat' => $threatEvents->first()?->created_at,
                'threat_frequency' => $threatCount / max(1, $totalEvents),
                'score' => $this->calculateHistoricalScore($threatCount, $totalEvents)
            ];
            
        } catch (\Exception $e) {
            Log::error('Historical Reputation Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return [
                'total_events' => 0,
                'threat_events' => 0,
                'last_threat' => null,
                'threat_frequency' => 0,
                'score' => 0
            ];
        }
    }

    /**
     * Análisis de comportamiento de amenazas
     */
    protected function analyzeThreatBehavior(string $ip): array
    {
        try {
            // Obtener eventos recientes (últimas 24 horas)
            $recentEvents = SecurityEvent::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subDay())
                ->get();
            
            if ($recentEvents->isEmpty()) {
                return [
                    'recent_threats' => 0,
                    'threat_patterns' => [],
                    'velocity_score' => 0,
                    'behavior_score' => 0
                ];
            }
            
            // Análisis de patrones de amenazas
            $threatPatterns = $this->extractThreatPatterns($recentEvents);
            
            // Cálculo de velocidad de amenazas
            $velocityScore = $this->calculateThreatVelocity($recentEvents);
            
            // Score de comportamiento
            $behaviorScore = $this->calculateBehaviorScore($threatPatterns, $velocityScore);
            
            return [
                'recent_threats' => $recentEvents->where('threat_score', '>=', 60)->count(),
                'threat_patterns' => $threatPatterns,
                'velocity_score' => $velocityScore,
                'behavior_score' => $behaviorScore
            ];
            
        } catch (\Exception $e) {
            Log::error('Threat Behavior Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return [
                'recent_threats' => 0,
                'threat_patterns' => [],
                'velocity_score' => 0,
                'behavior_score' => 0
            ];
        }
    }

    /**
     * Cálculo del score de amenaza final
     */
    protected function calculateFinalThreatScore(array $results, float $correlationScore, array $historicalAnalysis, array $behavioralAnalysis): float
    {
        try {
            // Score de proveedores externos
            $providerScore = $this->calculateProviderScore($results);
            
            // Score de correlación ML
            $mlScore = $correlationScore * 100;
            
            // Score histórico
            $historicalScore = $historicalAnalysis['score'];
            
            // Score de comportamiento
            $behaviorScore = $behavioralAnalysis['behavior_score'];
            
            // Pesos para el score final
            $weights = [
                'provider' => 0.35,
                'ml_correlation' => 0.25,
                'historical' => 0.25,
                'behavior' => 0.15
            ];
            
            $finalScore = ($providerScore * $weights['provider']) +
                         ($mlScore * $weights['ml_correlation']) +
                         ($historicalScore * $weights['historical']) +
                         ($behaviorScore * $weights['behavior']);
            
            return min(100, max(0, $finalScore));
            
        } catch (\Exception $e) {
            Log::error('Final Threat Score Calculation Error', [
                'error' => $e->getMessage()
            ]);
            
            return 50; // Score neutral en caso de error
        }
    }

    /**
     * Clasificación de amenazas
     */
    protected function classifyThreat(float $threatScore, array $results): array
    {
        if ($threatScore >= 80) {
            return [
                'level' => 'critical',
                'description' => 'Critical threat detected',
                'action' => 'immediate_block',
                'confidence' => 'high'
            ];
        } elseif ($threatScore >= 60) {
            return [
                'level' => 'high',
                'description' => 'High risk threat',
                'action' => 'enhanced_monitoring',
                'confidence' => 'medium'
            ];
        } elseif ($threatScore >= 40) {
            return [
                'level' => 'medium',
                'description' => 'Suspicious activity',
                'action' => 'monitor',
                'confidence' => 'medium'
            ];
        } elseif ($threatScore >= 20) {
            return [
                'level' => 'low',
                'description' => 'Low risk activity',
                'action' => 'log',
                'confidence' => 'low'
            ];
        } else {
            return [
                'level' => 'minimal',
                'description' => 'No threat detected',
                'action' => 'allow',
                'confidence' => 'high'
            ];
        }
    }

    /**
     * Generación de recomendaciones de seguridad
     */
    protected function generateRecommendations(float $threatScore, array $results): array
    {
        $recommendations = [];
        
        if ($threatScore >= 80) {
            $recommendations[] = 'Immediate IP blocking required';
            $recommendations[] = 'Investigate for data breach indicators';
            $recommendations[] = 'Update firewall rules immediately';
        } elseif ($threatScore >= 60) {
            $recommendations[] = 'Implement enhanced monitoring';
            $recommendations[] = 'Consider temporary IP restrictions';
            $recommendations[] = 'Review access logs for suspicious activity';
        } elseif ($threatScore >= 40) {
            $recommendations[] = 'Monitor IP behavior closely';
            $recommendations[] = 'Log all requests from this IP';
            $recommendations[] = 'Set up automated alerts';
        } elseif ($threatScore >= 20) {
            $recommendations[] = 'Continue normal monitoring';
            $recommendations[] = 'Log for trend analysis';
        } else {
            $recommendations[] = 'No immediate action required';
            $recommendations[] = 'Continue standard security practices';
        }
        
        return $recommendations;
    }

    /**
     * Métodos auxiliares de normalización
     */
    protected function normalizeAbuseIPDBScore(int $score): float
    {
        // AbuseIPDB usa 0-100, donde 100 es más malicioso
        return $score;
    }

    protected function normalizeVirusTotalScore(array $data): float
    {
        $detectedUrls = $data['detected_urls'] ?? 0;
        $detectedSamples = ($data['detected_communicating_samples'] ?? 0) + ($data['detected_downloaded_samples'] ?? 0);
        
        // Calcular score basado en detecciones
        $score = 0;
        if ($detectedUrls > 0) $score += 30;
        if ($detectedSamples > 0) $score += 40;
        
        return min(100, $score);
    }

    protected function normalizeIPQualityScore(array $data): float
    {
        $score = 0;
        
        if ($data['proxy'] ?? false) $score += 20;
        if ($data['vpn'] ?? false) $score += 15;
        if ($data['tor'] ?? false) $score += 25;
        if ($data['bot'] ?? false) $score += 30;
        
        $fraudScore = $data['fraud_score'] ?? 0;
        $score += min(40, $fraudScore * 0.4);
        
        return min(100, $score);
    }

    protected function normalizeIPAPIScore(array $data): float
    {
        // IP-API es principalmente geográfica, score bajo por defecto
        return 10;
    }

    /**
     * Métodos auxiliares de cálculo
     */
    protected function calculateProviderScore(array $results): float
    {
        $totalScore = 0;
        $totalWeight = 0;
        
        foreach ($this->config['scoring_weights'] as $provider => $weight) {
            if (isset($results[$provider]['score'])) {
                $totalScore += $results[$provider]['score'] * $weight;
                $totalWeight += $weight;
            }
        }
        
        return $totalWeight > 0 ? $totalScore / $totalWeight : 0;
    }

    protected function calculateHistoricalScore(int $threatCount, int $totalCount): float
    {
        if ($totalCount === 0) return 0;
        
        $threatRatio = $threatCount / $totalCount;
        return $threatRatio * 100;
    }

    protected function calculateThreatVelocity($events): float
    {
        if ($events->isEmpty()) return 0;
        
        $threatEvents = $events->where('threat_score', '>=', 60);
        $threatCount = $threatEvents->count();
        
        // Normalizar por hora
        return min(100, $threatCount * 10);
    }

    protected function calculateBehaviorScore(array $patterns, float $velocity): float
    {
        $patternScore = count($patterns) * 10;
        $velocityScore = $velocity;
        
        return min(100, ($patternScore + $velocityScore) / 2);
    }

    protected function calculateConfidence(array $results): float
    {
        $successfulProviders = count(array_filter($results, function($result) {
            return isset($result['score']) && $result['score'] >= 0;
        }));
        
        $totalProviders = count($this->config['apis']);
        
        return $totalProviders > 0 ? ($successfulProviders / $totalProviders) * 100 : 0;
    }

    protected function getDefaultProviderResult(string $provider): array
    {
        $defaults = [
            'abuseipdb' => ['score' => 0, 'abuse_confidence' => 0],
            'virustotal' => ['score' => 0, 'detected_urls' => 0],
            'ipqualityscore' => ['score' => 0, 'proxy' => false],
            'ipapi' => ['score' => 0, 'country' => null]
        ];
        
        return $defaults[$provider] ?? ['score' => 0];
    }

    protected function saveThreatIntelligence(string $ip, array $data): void
    {
        try {
            ThreatIntelligence::updateOrCreate(
                ['ip_address' => $ip],
                [
                    'threat_score' => $data['threat_score'],
                    'classification' => $data['classification']['level'],
                    'confidence' => $data['confidence'],
                    'data' => json_encode($data),
                    'last_updated' => now()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to save threat intelligence', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
        }
    }

    protected function fallbackThreatAnalysis(string $ip): array
    {
        return [
            'ip' => $ip,
            'threat_score' => 50,
            'classification' => [
                'level' => 'unknown',
                'description' => 'Analysis failed, using fallback',
                'action' => 'monitor',
                'confidence' => 'low'
            ],
            'confidence' => 0,
            'sources' => [],
            'correlation_score' => 0,
            'historical_analysis' => [],
            'behavioral_analysis' => [],
            'recommendations' => ['Use fallback analysis', 'Monitor IP behavior'],
            'last_updated' => now()->toISOString()
        ];
    }

    /**
     * Extraer patrones de amenazas
     */
    protected function extractThreatPatterns($recentEvents): array
    {
        if ($recentEvents->isEmpty()) return [];
        
        $patterns = [];
        
        foreach ($recentEvents as $event) {
            $patterns[] = [
                'type' => $event->event_type ?? 'unknown',
                'score' => $event->threat_score ?? 0,
                'timestamp' => $event->created_at
            ];
        }
        
        return $patterns;
    }
}
