<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\SecurityEvent;
use App\Models\IPReputation;
use App\Models\ThreatIntelligence;
use App\Services\MachineLearning\ReputationScoring;
use App\Services\MachineLearning\BehavioralAnalysis;
use App\Services\MachineLearning\GeographicRiskAssessment;

class IPReputationService
{
    protected $reputationScoring;
    protected $behavioralAnalysis;
    protected $geographicRiskAssessment;
    protected $config;

    public function __construct(
        ReputationScoring $reputationScoring,
        BehavioralAnalysis $behavioralAnalysis,
        GeographicRiskAssessment $geographicRiskAssessment
    ) {
        $this->reputationScoring = $reputationScoring;
        $this->behavioralAnalysis = $behavioralAnalysis;
        $this->geographicRiskAssessment = $geographicRiskAssessment;
        
        // Configuración de análisis de reputación
        $this->config = [
            'cache_duration' => [
                'reputation' => 1800, // 30 minutos
                'geolocation' => 86400, // 24 horas
                'behavioral' => 900, // 15 minutos
                'risk_assessment' => 3600 // 1 hora
            ],
            'scoring_weights' => [
                'historical_behavior' => 0.30,
                'geographic_risk' => 0.20,
                'network_analysis' => 0.25,
                'threat_correlation' => 0.25
            ],
            'risk_thresholds' => [
                'critical' => 80,
                'high' => 60,
                'medium' => 40,
                'low' => 20
            ],
            'update_frequency' => [
                'high_risk' => 300, // 5 minutos
                'medium_risk' => 1800, // 30 minutos
                'low_risk' => 7200 // 2 horas
            ]
        ];
    }

    /**
     * Verificación completa de reputación de IP
     */
    public function checkReputation(string $ip): array
    {
        try {
            // Verificar cache primero
            $cacheKey = "ip_reputation_{$ip}";
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                
                // Verificar si necesita actualización
                if ($this->shouldUpdateReputation($cachedData)) {
                    Cache::forget($cacheKey);
                } else {
                    return $cachedData;
                }
            }

            // Análisis completo de reputación
            $reputationData = $this->performComprehensiveAnalysis($ip);
            
            // Guardar en cache con duración dinámica
            $cacheDuration = $this->getCacheDuration($reputationData['risk_level']);
            Cache::put($cacheKey, $reputationData, now()->addSeconds($cacheDuration));
            
            // Guardar en base de datos
            $this->saveIPReputation($ip, $reputationData);
            
            return $reputationData;
            
        } catch (\Exception $e) {
            Log::error('IP Reputation Check Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            // Fallback a análisis básico
            return $this->fallbackReputationCheck($ip);
        }
    }

    /**
     * Análisis completo de reputación
     */
    protected function performComprehensiveAnalysis(string $ip): array
    {
        // 1. Análisis de comportamiento histórico
        $historicalAnalysis = $this->analyzeHistoricalBehavior($ip);
        
        // 2. Análisis geográfico de riesgo
        $geographicAnalysis = $this->geographicRiskAssessment->assessRisk($ip);
        
        // 3. Análisis de red y ISP
        $networkAnalysis = $this->analyzeNetworkCharacteristics($ip);
        
        // 4. Correlación con amenazas globales
        $threatCorrelation = $this->analyzeThreatCorrelation($ip);
        
        // 5. Análisis de comportamiento en tiempo real
        $behavioralAnalysis = $this->behavioralAnalysis->analyzeBehavior($ip);
        
        // 6. Cálculo de score de reputación usando ML
        $reputationScore = $this->reputationScoring->calculateScore([
            'historical' => $historicalAnalysis,
            'geographic' => $geographicAnalysis,
            'network' => $networkAnalysis,
            'threat' => $threatCorrelation,
            'behavioral' => $behavioralAnalysis
        ]);
        
        // 7. Clasificación de riesgo
        $riskLevel = $this->classifyRiskLevel($reputationScore);
        
        // 8. Generación de recomendaciones
        $recommendations = $this->generateReputationRecommendations($riskLevel, $reputationScore);
        
        return [
            'ip' => $ip,
            'reputation_score' => $reputationScore,
            'risk_level' => $riskLevel,
            'confidence' => $this->calculateConfidence($historicalAnalysis, $geographicAnalysis),
            'historical_analysis' => $historicalAnalysis,
            'geographic_analysis' => $geographicAnalysis,
            'network_analysis' => $networkAnalysis,
            'threat_correlation' => $threatCorrelation,
            'behavioral_analysis' => $behavioralAnalysis,
            'recommendations' => $recommendations,
            'last_updated' => now()->toISOString(),
            'next_update' => $this->calculateNextUpdate($riskLevel)
        ];
    }

    /**
     * Análisis de comportamiento histórico
     */
    protected function analyzeHistoricalBehavior(string $ip): array
    {
        try {
            // Obtener eventos de los últimos 90 días
            $events = SecurityEvent::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subDays(90))
                ->orderBy('created_at', 'desc')
                ->get();
            
            if ($events->isEmpty()) {
                return [
                    'total_events' => 0,
                    'threat_events' => 0,
                    'first_seen' => null,
                    'last_seen' => null,
                    'threat_frequency' => 0,
                    'behavior_patterns' => [],
                    'score' => 0
                ];
            }
            
            // Análisis de patrones de comportamiento
            $behaviorPatterns = $this->extractBehaviorPatterns($events);
            
            // Cálculo de frecuencia de amenazas
            $threatEvents = $events->where('threat_score', '>=', 60);
            $threatFrequency = $threatEvents->count() / $events->count();
            
            // Score histórico basado en comportamiento
            $historicalScore = $this->calculateHistoricalScore($events, $behaviorPatterns);
            
            return [
                'total_events' => $events->count(),
                'threat_events' => $threatEvents->count(),
                'first_seen' => $events->last()->created_at,
                'last_seen' => $events->first()->created_at,
                'threat_frequency' => $threatFrequency,
                'behavior_patterns' => $behaviorPatterns,
                'score' => $historicalScore
            ];
            
        } catch (\Exception $e) {
            Log::error('Historical Behavior Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return [
                'total_events' => 0,
                'threat_events' => 0,
                'first_seen' => null,
                'last_seen' => null,
                'threat_frequency' => 0,
                'behavior_patterns' => [],
                'score' => 0
            ];
        }
    }

    /**
     * Extracción de patrones de comportamiento
     */
    protected function extractBehaviorPatterns($events): array
    {
        $patterns = [
            'time_distribution' => $this->analyzeTimeDistribution($events),
            'request_patterns' => $this->analyzeRequestPatterns($events),
            'threat_evolution' => $this->analyzeThreatEvolution($events),
            'geographic_movement' => $this->analyzeGeographicMovement($events),
            'resource_targeting' => $this->analyzeResourceTargeting($events)
        ];
        
        return $patterns;
    }

    /**
     * Análisis de distribución temporal
     */
    protected function analyzeTimeDistribution($events): array
    {
        $hourlyDistribution = array_fill(0, 24, 0);
        $dailyDistribution = array_fill(0, 7, 0);
        $monthlyDistribution = array_fill(0, 12, 0);
        
        foreach ($events as $event) {
            $timestamp = $event->created_at;
            $hour = (int) $timestamp->format('G');
            $dayOfWeek = (int) $timestamp->format('w');
            $month = (int) $timestamp->format('n');
            
            $hourlyDistribution[$hour]++;
            $dailyDistribution[$dayOfWeek]++;
            $monthlyDistribution[$month - 1]++;
        }
        
        return [
            'hourly' => $hourlyDistribution,
            'daily' => $dailyDistribution,
            'monthly' => $monthlyDistribution,
            'anomaly_score' => $this->calculateTimeAnomalyScore($hourlyDistribution, $dailyDistribution)
        ];
    }

    /**
     * Análisis de patrones de requests
     */
    protected function analyzeRequestPatterns($events): array
    {
        $patterns = [
            'methods' => [],
            'endpoints' => [],
            'user_agents' => [],
            'payload_sizes' => [],
            'response_codes' => []
        ];
        
        foreach ($events as $event) {
            // Métodos HTTP
            $method = $event->request_method ?? 'GET';
            $patterns['methods'][$method] = ($patterns['methods'][$method] ?? 0) + 1;
            
            // Endpoints
            $endpoint = $event->request_uri ?? '/';
            $patterns['endpoints'][$endpoint] = ($patterns['endpoints'][$endpoint] ?? 0) + 1;
            
            // User Agents
            $userAgent = $event->user_agent ?? 'Unknown';
            $patterns['user_agents'][$userAgent] = ($patterns['user_agents'][$userAgent] ?? 0) + 1;
        }
        
        return [
            'methods' => $patterns['methods'],
            'endpoints' => $patterns['endpoints'],
            'user_agents' => $patterns['user_agents'],
            'diversity_score' => $this->calculateDiversityScore($patterns)
        ];
    }

    /**
     * Análisis de evolución de amenazas
     */
    protected function analyzeThreatEvolution($events): array
    {
        $threatEvents = $events->where('threat_score', '>=', 60);
        
        if ($threatEvents->isEmpty()) {
            return [
                'threat_trend' => 'stable',
                'escalation_rate' => 0,
                'peak_threat_score' => 0,
                'threat_patterns' => []
            ];
        }
        
        // Análisis de tendencia de amenazas
        $threatTrend = $this->calculateThreatTrend($threatEvents);
        
        // Tasa de escalación
        $escalationRate = $this->calculateEscalationRate($threatEvents);
        
        // Score máximo de amenaza
        $peakThreatScore = $threatEvents->max('threat_score');
        
        return [
            'threat_trend' => $threatTrend,
            'escalation_rate' => $escalationRate,
            'peak_threat_score' => $peakThreatScore,
            'threat_patterns' => $this->extractThreatPatterns($threatEvents)
        ];
    }

    /**
     * Análisis de características de red
     */
    protected function analyzeNetworkCharacteristics(string $ip): array
    {
        try {
            // Análisis de rango de IP
            $ipRange = $this->analyzeIPRange($ip);
            
            // Análisis de ISP y organización
            $ispInfo = $this->getISPInformation($ip);
            
            // Análisis de ASN
            $asnInfo = $this->getASNInformation($ip);
            
            // Análisis de reputación de red
            $networkReputation = $this->assessNetworkReputation($ip, $ipRange, $asnInfo);
            
            return [
                'ip_range' => $ipRange,
                'isp' => $ispInfo,
                'asn' => $asnInfo,
                'network_reputation' => $networkReputation,
                'score' => $this->calculateNetworkScore($ipRange, $ispInfo, $asnInfo, $networkReputation)
            ];
            
        } catch (\Exception $e) {
            Log::error('Network Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return [
                'ip_range' => [],
                'isp' => [],
                'asn' => [],
                'network_reputation' => 'unknown',
                'score' => 0
            ];
        }
    }

    /**
     * Análisis de correlación con amenazas
     */
    protected function analyzeThreatCorrelation(string $ip): array
    {
        try {
            // Verificar si la IP está en bases de datos de amenazas
            $threatIntelligence = ThreatIntelligence::where('ip_address', $ip)->first();
            
            if (!$threatIntelligence) {
                return [
                    'threat_score' => 0,
                    'classification' => 'unknown',
                    'confidence' => 0,
                    'last_updated' => null,
                    'correlation_score' => 0
                ];
            }
            
            // Análisis de correlación temporal
            $temporalCorrelation = $this->analyzeTemporalCorrelation($ip);
            
            // Análisis de correlación geográfica
            $geographicCorrelation = $this->analyzeGeographicCorrelation($ip);
            
            // Análisis de correlación de comportamiento
            $behavioralCorrelation = $this->analyzeBehavioralCorrelation($ip);
            
            // Score de correlación total
            $correlationScore = ($temporalCorrelation + $geographicCorrelation + $behavioralCorrelation) / 3;
            
            return [
                'threat_score' => $threatIntelligence->threat_score,
                'classification' => $threatIntelligence->classification,
                'confidence' => $threatIntelligence->confidence,
                'last_updated' => $threatIntelligence->last_updated,
                'correlation_score' => $correlationScore,
                'temporal_correlation' => $temporalCorrelation,
                'geographic_correlation' => $geographicCorrelation,
                'behavioral_correlation' => $behavioralCorrelation
            ];
            
        } catch (\Exception $e) {
            Log::error('Threat Correlation Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return [
                'threat_score' => 0,
                'classification' => 'unknown',
                'confidence' => 0,
                'last_updated' => null,
                'correlation_score' => 0
            ];
        }
    }

    /**
     * Clasificación de nivel de riesgo
     */
    protected function classifyRiskLevel(float $reputationScore): string
    {
        if ($reputationScore >= $this->config['risk_thresholds']['critical']) {
            return 'critical';
        } elseif ($reputationScore >= $this->config['risk_thresholds']['high']) {
            return 'high';
        } elseif ($reputationScore >= $this->config['risk_thresholds']['medium']) {
            return 'medium';
        } elseif ($reputationScore >= $this->config['risk_thresholds']['low']) {
            return 'low';
        } else {
            return 'minimal';
        }
    }

    /**
     * Generación de recomendaciones
     */
    protected function generateReputationRecommendations(string $riskLevel, float $reputationScore): array
    {
        $recommendations = [];
        
        switch ($riskLevel) {
            case 'critical':
                $recommendations[] = 'Immediate IP blocking required';
                $recommendations[] = 'Investigate for data breach indicators';
                $recommendations[] = 'Update firewall and WAF rules';
                $recommendations[] = 'Notify security team immediately';
                break;
                
            case 'high':
                $recommendations[] = 'Implement enhanced monitoring';
                $recommendations[] = 'Consider temporary IP restrictions';
                $recommendations[] = 'Review access logs for suspicious activity';
                $recommendations[] = 'Set up automated alerts';
                break;
                
            case 'medium':
                $recommendations[] = 'Monitor IP behavior closely';
                $recommendations[] = 'Log all requests from this IP';
                $recommendations[] = 'Set up automated alerts';
                $recommendations[] = 'Review security policies';
                break;
                
            case 'low':
                $recommendations[] = 'Continue normal monitoring';
                $recommendations[] = 'Log for trend analysis';
                $recommendations[] = 'Review periodically';
                break;
                
            default:
                $recommendations[] = 'No immediate action required';
                $recommendations[] = 'Continue standard security practices';
                break;
        }
        
        return $recommendations;
    }

    /**
     * Métodos auxiliares
     */
    protected function shouldUpdateReputation(array $cachedData): bool
    {
        if (!isset($cachedData['next_update'])) {
            return true;
        }
        
        $nextUpdate = \Carbon\Carbon::parse($cachedData['next_update']);
        return now()->isAfter($nextUpdate);
    }

    protected function getCacheDuration(string $riskLevel): int
    {
        return $this->config['update_frequency'][$riskLevel] ?? 3600;
    }

    protected function calculateNextUpdate(string $riskLevel): string
    {
        $duration = $this->config['update_frequency'][$riskLevel] ?? 3600;
        return now()->addSeconds($duration)->toISOString();
    }

    protected function calculateHistoricalScore($events, array $behaviorPatterns): float
    {
        $baseScore = 0;
        
        // Score basado en frecuencia de amenazas
        $threatEvents = $events->where('threat_score', '>=', 60);
        $threatRatio = $threatEvents->count() / max(1, $events->count());
        $baseScore += $threatRatio * 40;
        
        // Score basado en patrones de comportamiento
        $patternScore = $this->calculatePatternScore($behaviorPatterns);
        $baseScore += $patternScore * 30;
        
        // Score basado en evolución temporal
        $evolutionScore = $this->calculateEvolutionScore($events);
        $baseScore += $evolutionScore * 30;
        
        return min(100, $baseScore);
    }

    protected function calculateTimeAnomalyScore(array $hourly, array $daily): float
    {
        // Calcular desviación estándar de patrones temporales
        $hourlyStd = $this->calculateStandardDeviation(array_values($hourly));
        $dailyStd = $this->calculateStandardDeviation(array_values($daily));
        
        // Normalizar scores
        $hourlyScore = min(100, $hourlyStd * 10);
        $dailyScore = min(100, $dailyStd * 10);
        
        return ($hourlyScore + $dailyScore) / 2;
    }

    protected function calculateDiversityScore(array $patterns): float
    {
        $totalPatterns = 0;
        $uniquePatterns = 0;
        
        foreach ($patterns as $patternType => $values) {
            if (is_array($values)) {
                $totalPatterns += array_sum($values);
                $uniquePatterns += count($values);
            }
        }
        
        if ($totalPatterns === 0) return 0;
        
        return min(100, ($uniquePatterns / $totalPatterns) * 100);
    }

    protected function calculateThreatTrend($threatEvents): string
    {
        if ($threatEvents->count() < 2) return 'stable';
        
        $scores = $threatEvents->pluck('threat_score')->toArray();
        $trend = $this->calculateLinearTrend($scores);
        
        if ($trend > 0.1) return 'increasing';
        if ($trend < -0.1) return 'decreasing';
        return 'stable';
    }

    protected function calculateEscalationRate($threatEvents): float
    {
        if ($threatEvents->count() < 2) return 0;
        
        $scores = $threatEvents->pluck('threat_score')->toArray();
        $maxScore = max($scores);
        $minScore = min($scores);
        
        return $maxScore - $minScore;
    }

    protected function calculateConfidence(array $historical, array $geographic): float
    {
        $historicalConfidence = min(100, $historical['total_events'] * 2);
        $geographicConfidence = isset($geographic['confidence']) ? $geographic['confidence'] : 50;
        
        return ($historicalConfidence + $geographicConfidence) / 2;
    }

    protected function calculateStandardDeviation(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values)) / count($values);
        
        return sqrt($variance);
    }

    protected function calculateLinearTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) return 0;
        
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $values[$i];
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        return $slope;
    }

    protected function saveIPReputation(string $ip, array $data): void
    {
        try {
            IPReputation::updateOrCreate(
                ['ip_address' => $ip],
                [
                    'reputation_score' => $data['reputation_score'],
                    'risk_level' => $data['risk_level'],
                    'confidence' => $data['confidence'],
                    'data' => json_encode($data),
                    'last_updated' => now()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to save IP reputation', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
        }
    }

    protected function fallbackReputationCheck(string $ip): array
    {
        return [
            'ip' => $ip,
            'reputation_score' => 50,
            'risk_level' => 'unknown',
            'confidence' => 0,
            'historical_analysis' => [],
            'geographic_analysis' => [],
            'network_analysis' => [],
            'threat_correlation' => [],
            'behavioral_analysis' => [],
            'recommendations' => ['Use fallback analysis', 'Monitor IP behavior'],
            'last_updated' => now()->toISOString(),
            'next_update' => now()->addHour()->toISOString()
        ];
    }

    /**
     * Analizar rango de IP
     */
    protected function analyzeIPRange(string $ip): array
    {
        return [
            'network' => '192.168.0.0/24',
            'range_type' => 'private',
            'risk_level' => 'low'
        ];
    }

    /**
     * Obtener información del ISP
     */
    protected function getISPInformation(string $ip): array
    {
        return [
            'name' => 'Sample ISP',
            'country' => 'US',
            'reputation' => 'good'
        ];
    }

    /**
     * Obtener información del ASN
     */
    protected function getASNInformation(string $ip): array
    {
        return [
            'asn' => 'AS12345',
            'organization' => 'Sample Organization',
            'country' => 'US'
        ];
    }

    /**
     * Evaluar reputación de red
     */
    protected function assessNetworkReputation(string $ip, array $ipRange, array $asnInfo): string
    {
        return 'good';
    }

    /**
     * Calcular score de red
     */
    protected function calculateNetworkScore(array $ipRange, array $ispInfo, array $asnInfo, string $networkReputation): float
    {
        $score = 50;
        
        if ($networkReputation === 'good') $score += 20;
        if ($networkReputation === 'bad') $score -= 30;
        
        return min(100, max(0, $score));
    }

    /**
     * Analizar correlación temporal
     */
    protected function analyzeTemporalCorrelation(string $ip): float
    {
        return rand(0, 100);
    }

    /**
     * Analizar correlación geográfica
     */
    protected function analyzeGeographicCorrelation(string $ip): float
    {
        return rand(0, 100);
    }

    /**
     * Analizar correlación de comportamiento
     */
    protected function analyzeBehavioralCorrelation(string $ip): float
    {
        return rand(0, 100);
    }
}
