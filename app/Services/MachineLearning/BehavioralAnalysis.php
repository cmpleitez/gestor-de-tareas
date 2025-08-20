<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BehavioralAnalysis
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'analysis_window' => 86400, // 24 horas
            'min_data_points' => 10,
            'anomaly_threshold' => 0.8
        ];
    }

    /**
     * Analizar comportamiento de una IP
     */
    public function analyzeBehavior(string $ip): array
    {
        try {
            $cacheKey = "behavioral_analysis_{$ip}";
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Análisis de patrones de comportamiento
            $behaviorPatterns = $this->extractBehaviorPatterns($ip);
            
            // Análisis de anomalías
            $anomalyScore = $this->detectAnomalies($ip, $behaviorPatterns);
            
            // Análisis de consistencia
            $consistencyScore = $this->analyzeConsistency($behaviorPatterns);
            
            // Score de comportamiento final
            $behaviorScore = $this->calculateBehaviorScore($anomalyScore, $consistencyScore, $behaviorPatterns);
            
            $result = [
                'anomaly_score' => $anomalyScore,
                'consistency_score' => $consistencyScore,
                'behavior_score' => $behaviorScore,
                'patterns' => $behaviorPatterns,
                'risk_level' => $this->classifyBehaviorRisk($behaviorScore),
                'confidence' => $this->calculateConfidence($behaviorPatterns),
                'last_updated' => now()->toISOString()
            ];
            
            // Cache por 15 minutos
            Cache::put($cacheKey, $result, now()->addMinutes(15));
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Behavioral Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return $this->getDefaultBehaviorResult();
        }
    }

    /**
     * Extraer patrones de comportamiento
     */
    protected function extractBehaviorPatterns(string $ip): array
    {
        $patterns = [
            'request_frequency' => $this->analyzeRequestFrequency($ip),
            'time_patterns' => $this->analyzeTimePatterns($ip),
            'geographic_patterns' => $this->analyzeGeographicPatterns($ip),
            'resource_patterns' => $this->analyzeResourcePatterns($ip),
            'error_patterns' => $this->analyzeErrorPatterns($ip)
        ];
        
        return $patterns;
    }

    /**
     * Analizar frecuencia de requests
     */
    protected function analyzeRequestFrequency(string $ip): array
    {
        $frequencies = [
            'per_minute' => rand(1, 10), // Simulado por ahora
            'per_hour' => rand(10, 100),
            'per_day' => rand(100, 1000),
            'peak_hours' => [9, 14, 18], // Horas pico simuladas
            'velocity_score' => rand(20, 80)
        ];
        
        return $frequencies;
    }

    /**
     * Analizar patrones temporales
     */
    protected function analyzeTimePatterns(string $ip): array
    {
        $patterns = [
            'hourly_distribution' => array_fill(0, 24, rand(0, 50)),
            'daily_distribution' => array_fill(0, 7, rand(0, 200)),
            'weekend_activity' => rand(0, 100),
            'night_activity' => rand(0, 100),
            'anomaly_score' => rand(0, 100)
        ];
        
        return $patterns;
    }

    /**
     * Analizar patrones geográficos
     */
    protected function analyzeGeographicPatterns(string $ip): array
    {
        $patterns = [
            'country_consistency' => rand(50, 100),
            'region_changes' => rand(0, 10),
            'timezone_anomalies' => rand(0, 100),
            'geographic_velocity' => rand(0, 100)
        ];
        
        return $patterns;
    }

    /**
     * Analizar patrones de recursos
     */
    protected function analyzeResourcePatterns(string $ip): array
    {
        $patterns = [
            'endpoint_diversity' => rand(10, 100),
            'resource_targeting' => rand(0, 100),
            'payload_patterns' => rand(0, 100),
            'session_patterns' => rand(0, 100)
        ];
        
        return $patterns;
    }

    /**
     * Analizar patrones de errores
     */
    protected function analyzeErrorPatterns(string $ip): array
    {
        $patterns = [
            'error_frequency' => rand(0, 100),
            'error_types' => ['404', '403', '500'],
            'error_patterns' => rand(0, 100),
            'attack_indicators' => rand(0, 100)
        ];
        
        return $patterns;
    }

    /**
     * Detectar anomalías en el comportamiento
     */
    protected function detectAnomalies(string $ip, array $patterns): float
    {
        $anomalyScores = [];
        
        // Análisis de frecuencia anómala
        if (isset($patterns['request_frequency']['velocity_score'])) {
            $velocityScore = $patterns['request_frequency']['velocity_score'];
            if ($velocityScore > 70) {
                $anomalyScores[] = 0.8;
            } elseif ($velocityScore > 50) {
                $anomalyScores[] = 0.6;
            } else {
                $anomalyScores[] = 0.2;
            }
        }
        
        // Análisis de patrones temporales anómalos
        if (isset($patterns['time_patterns']['anomaly_score'])) {
            $timeAnomaly = $patterns['time_patterns']['anomaly_score'] / 100;
            $anomalyScores[] = $timeAnomaly;
        }
        
        // Análisis de actividad nocturna sospechosa
        if (isset($patterns['time_patterns']['night_activity'])) {
            $nightActivity = $patterns['time_patterns']['night_activity'] / 100;
            if ($nightActivity > 0.7) {
                $anomalyScores[] = 0.9;
            } else {
                $anomalyScores[] = 0.3;
            }
        }
        
        // Análisis de cambios geográficos rápidos
        if (isset($patterns['geographic_patterns']['geographic_velocity'])) {
            $geoVelocity = $patterns['geographic_patterns']['geographic_velocity'] / 100;
            if ($geoVelocity > 0.8) {
                $anomalyScores[] = 0.9;
            } else {
                $anomalyScores[] = 0.2;
            }
        }
        
        if (empty($anomalyScores)) return 0.5;
        
        return array_sum($anomalyScores) / count($anomalyScores);
    }

    /**
     * Analizar consistencia del comportamiento
     */
    protected function analyzeConsistency(array $patterns): float
    {
        $consistencyFactors = [];
        
        // Consistencia geográfica
        if (isset($patterns['geographic_patterns']['country_consistency'])) {
            $consistencyFactors[] = $patterns['geographic_patterns']['country_consistency'] / 100;
        }
        
        // Consistencia temporal
        if (isset($patterns['time_patterns']['anomaly_score'])) {
            $timeConsistency = 1 - ($patterns['time_patterns']['anomaly_score'] / 100);
            $consistencyFactors[] = $timeConsistency;
        }
        
        // Consistencia de recursos
        if (isset($patterns['resource_patterns']['endpoint_diversity'])) {
            $resourceConsistency = 1 - ($patterns['resource_patterns']['endpoint_diversity'] / 100);
            $consistencyFactors[] = $resourceConsistency;
        }
        
        if (empty($consistencyFactors)) return 0.5;
        
        return array_sum($consistencyFactors) / count($consistencyFactors);
    }

    /**
     * Calcular score de comportamiento final
     */
    protected function calculateBehaviorScore(float $anomalyScore, float $consistencyScore, array $patterns): float
    {
        // Score base basado en anomalías
        $baseScore = $anomalyScore * 100;
        
        // Ajustar por consistencia
        $consistencyAdjustment = $consistencyScore * 20;
        $baseScore += $consistencyAdjustment;
        
        // Ajustar por patrones específicos
        $patternAdjustment = $this->calculatePatternAdjustment($patterns);
        $baseScore += $patternAdjustment;
        
        return min(100, max(0, $baseScore));
    }

    /**
     * Calcular ajuste por patrones específicos
     */
    protected function calculatePatternAdjustment(array $patterns): float
    {
        $adjustment = 0;
        
        // Ajuste por actividad nocturna
        if (isset($patterns['time_patterns']['night_activity'])) {
            $nightActivity = $patterns['time_patterns']['night_activity'] / 100;
            if ($nightActivity > 0.7) {
                $adjustment += 15;
            }
        }
        
        // Ajuste por velocidad geográfica
        if (isset($patterns['geographic_patterns']['geographic_velocity'])) {
            $geoVelocity = $patterns['geographic_patterns']['geographic_velocity'] / 100;
            if ($geoVelocity > 0.8) {
                $adjustment += 20;
            }
        }
        
        // Ajuste por indicadores de ataque
        if (isset($patterns['error_patterns']['attack_indicators'])) {
            $attackIndicators = $patterns['error_patterns']['attack_indicators'] / 100;
            $adjustment += $attackIndicators * 25;
        }
        
        return $adjustment;
    }

    /**
     * Clasificar nivel de riesgo del comportamiento
     */
    protected function classifyBehaviorRisk(float $behaviorScore): string
    {
        if ($behaviorScore >= 80) return 'critical';
        if ($behaviorScore >= 60) return 'high';
        if ($behaviorScore >= 40) return 'medium';
        if ($behaviorScore >= 20) return 'low';
        return 'minimal';
    }

    /**
     * Calcular confianza del análisis
     */
    protected function calculateConfidence(array $patterns): float
    {
        $confidenceFactors = [];
        
        // Confianza basada en cantidad de datos
        $dataPoints = 0;
        foreach ($patterns as $pattern) {
            if (is_array($pattern)) {
                $dataPoints += count($pattern);
            }
        }
        
        $dataConfidence = min(1.0, $dataPoints / 100);
        $confidenceFactors[] = $dataConfidence;
        
        // Confianza basada en consistencia de patrones
        if (isset($patterns['time_patterns']['anomaly_score'])) {
            $timeConfidence = 1 - ($patterns['time_patterns']['anomaly_score'] / 100);
            $confidenceFactors[] = $timeConfidence;
        }
        
        if (empty($confidenceFactors)) return 0.7;
        
        return array_sum($confidenceFactors) / count($confidenceFactors);
    }

    /**
     * Resultado por defecto en caso de error
     */
    protected function getDefaultBehaviorResult(): array
    {
        return [
            'anomaly_score' => 0.5,
            'consistency_score' => 0.5,
            'behavior_score' => 50,
            'patterns' => [],
            'risk_level' => 'unknown',
            'confidence' => 0.5,
            'last_updated' => now()->toISOString()
        ];
    }
}
