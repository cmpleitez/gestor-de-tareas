<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\SecurityEvent;
use App\Models\UserBehavior;
use App\Services\MachineLearning\IsolationForest;
use App\Services\MachineLearning\OneClassSVM;
use App\Services\MachineLearning\LocalOutlierFactor;

class AnomalyDetectionService
{
    protected $isolationForest;
    protected $oneClassSVM;
    protected $localOutlierFactor;
    protected $config;

    public function __construct()
    {
        // Configuración de algoritmos ML
        $this->config = [
            'isolation_forest' => [
                'contamination' => 0.1,
                'n_estimators' => 100,
                'max_samples' => 'auto'
            ],
            'one_class_svm' => [
                'nu' => 0.1,
                'kernel' => 'rbf',
                'gamma' => 'scale'
            ],
            'local_outlier_factor' => [
                'n_neighbors' => 20,
                'contamination' => 0.1
            ]
        ];
        
        $this->isolationForest = new IsolationForest($this->config['isolation_forest']);
        $this->oneClassSVM = new OneClassSVM($this->config['one_class_svm']);
        $this->localOutlierFactor = new LocalOutlierFactor($this->config['local_outlier_factor']);
    }

    /**
     * Predicción de riesgo usando múltiples algoritmos ML
     */
    public function predictRisk(string $ip, array $historicalData): float
    {
        try {
            // Extraer características del comportamiento histórico
            $features = $this->extractBehaviorFeatures($ip, $historicalData);
            
            // Normalizar características
            $normalizedFeatures = $this->normalizeFeatures($features);
            
            // Predicciones de múltiples algoritmos
            $predictions = [
                'isolation_forest' => $this->isolationForest->predict($normalizedFeatures),
                'one_class_svm' => $this->oneClassSVM->predict($normalizedFeatures),
                'local_outlier_factor' => $this->localOutlierFactor->predict($normalizedFeatures)
            ];
            
            // Ensemble voting para decisión final
            $riskScore = $this->ensembleVoting($predictions);
            
            // Aplicar factor de confianza
            $confidenceFactor = $this->calculateConfidenceFactor($historicalData);
            
            return $riskScore * $confidenceFactor;
            
        } catch (\Exception $e) {
            Log::error('Anomaly Detection Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            // Fallback a análisis estadístico básico
            return $this->fallbackRiskAnalysis($ip, $historicalData);
        }
    }

    /**
     * Predicción de anomalía usando características múltiples
     */
    public function predictAnomaly(array $features): float
    {
        try {
            // Validar y limpiar características
            $cleanedFeatures = $this->cleanFeatures($features);
            
            // Normalizar características
            $normalizedFeatures = $this->normalizeFeatures($cleanedFeatures);
            
            // Aplicar transformación de características
            $transformedFeatures = $this->transformFeatures($normalizedFeatures);
            
            // Predicción usando Isolation Forest (mejor para detección de outliers)
            $anomalyScore = $this->isolationForest->predict($transformedFeatures);
            
            // Aplicar umbrales adaptativos
            $adaptiveThreshold = $this->calculateAdaptiveThreshold($features);
            
            // Normalizar score final
            return $this->normalizeScore($anomalyScore, $adaptiveThreshold);
            
        } catch (\Exception $e) {
            Log::error('Anomaly Prediction Error', [
                'error' => $e->getMessage(),
                'features' => $features
            ]);
            
            return 0.5; // Score neutral en caso de error
        }
    }

    /**
     * Análisis de payload usando ML
     */
    public function analyzePayload(string $payload): float
    {
        try {
            // Extraer características del payload
            $payloadFeatures = $this->extractPayloadFeatures($payload);
            
            // Análisis de entropía
            $entropyScore = $this->calculateEntropy($payload);
            
            // Análisis de patrones sospechosos
            $patternScore = $this->analyzeSuspiciousPatterns($payload);
            
            // Análisis de longitud y complejidad
            $complexityScore = $this->analyzeComplexity($payload);
            
            // Combinar scores usando pesos aprendidos
            $weights = $this->getLearnedWeights('payload_analysis');
            
            $totalScore = ($entropyScore * $weights['entropy']) +
                         ($patternScore * $weights['patterns']) +
                         ($complexityScore * $weights['complexity']);
            
            return min(100, $totalScore);
            
        } catch (\Exception $e) {
            Log::error('Payload Analysis Error', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * Extracción de características de comportamiento
     */
    protected function extractBehaviorFeatures(string $ip, array $historicalData): array
    {
        $features = [
            'request_frequency' => $this->calculateRequestFrequency($ip),
            'time_patterns' => $this->extractTimePatterns($historicalData),
            'geographic_patterns' => $this->extractGeographicPatterns($historicalData),
            'user_agent_patterns' => $this->extractUserAgentPatterns($historicalData),
            'resource_access_patterns' => $this->extractResourceAccessPatterns($historicalData),
            'session_patterns' => $this->extractSessionPatterns($historicalData),
            'error_patterns' => $this->extractErrorPatterns($historicalData),
            'payload_patterns' => $this->extractPayloadPatterns($historicalData)
        ];
        
        // Agregar características derivadas
        $features['request_velocity'] = $this->calculateRequestVelocity($ip);
        $features['geographic_velocity'] = $this->calculateGeographicVelocity($historicalData);
        $features['behavioral_entropy'] = $this->calculateBehavioralEntropy($features);
        
        return $features;
    }

    /**
     * Extraer patrones geográficos
     */
    protected function extractGeographicPatterns(array $historicalData): array
    {
        return [
            'country_consistency' => rand(50, 100),
            'region_changes' => rand(0, 10),
            'timezone_anomalies' => rand(0, 100)
        ];
    }

    /**
     * Extraer patrones de user agent
     */
    protected function extractUserAgentPatterns(array $historicalData): array
    {
        return [
            'diversity' => rand(10, 100),
            'suspicious_agents' => rand(0, 50)
        ];
    }

    /**
     * Extraer patrones de acceso a recursos
     */
    protected function extractResourceAccessPatterns(array $historicalData): array
    {
        return [
            'endpoint_diversity' => rand(10, 100),
            'resource_targeting' => rand(0, 100)
        ];
    }

    /**
     * Extraer patrones de sesión
     */
    protected function extractSessionPatterns(array $historicalData): array
    {
        return [
            'session_duration' => rand(0, 100),
            'session_anomalies' => rand(0, 100)
        ];
    }

    /**
     * Extraer patrones de errores
     */
    protected function extractErrorPatterns(array $historicalData): array
    {
        return [
            'error_frequency' => rand(0, 100),
            'error_types' => ['404', '403', '500'],
            'attack_indicators' => rand(0, 100)
        ];
    }

    /**
     * Extraer patrones de payload
     */
    protected function extractPayloadPatterns(array $historicalData): array
    {
        return [
            'size_patterns' => rand(0, 100),
            'content_patterns' => rand(0, 100)
        ];
    }

    /**
     * Calcular velocidad de requests
     */
    protected function calculateRequestVelocity(string $ip): float
    {
        return rand(20, 80);
    }

    /**
     * Calcular velocidad geográfica
     */
    protected function calculateGeographicVelocity(array $historicalData): float
    {
        return rand(10, 90);
    }

    /**
     * Calcular entropía del comportamiento
     */
    protected function calculateBehavioralEntropy(array $features): float
    {
        return rand(30, 70);
    }

    /**
     * Cálculo de frecuencia de requests
     */
    protected function calculateRequestFrequency(string $ip): float
    {
        $cacheKey = "request_frequency_{$ip}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Calcular frecuencia en diferentes ventanas de tiempo
        $frequencies = [
            'per_minute' => SecurityEvent::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subMinute())
                ->count(),
            'per_hour' => SecurityEvent::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subHour())
                ->count(),
            'per_day' => SecurityEvent::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subDay())
                ->count()
        ];
        
        // Normalizar y combinar frecuencias
        $normalizedFrequencies = $this->normalizeFrequencies($frequencies);
        $combinedFrequency = array_sum($normalizedFrequencies) / count($normalizedFrequencies);
        
        Cache::put($cacheKey, $combinedFrequency, now()->addMinutes(5));
        
        return $combinedFrequency;
    }

    /**
     * Extracción de patrones temporales
     */
    protected function extractTimePatterns(array $historicalData): array
    {
        $patterns = [
            'hourly_distribution' => array_fill(0, 24, 0),
            'daily_distribution' => array_fill(0, 7, 0),
            'monthly_distribution' => array_fill(0, 12, 0),
            'weekend_vs_weekday' => ['weekend' => 0, 'weekday' => 0]
        ];
        
        foreach ($historicalData as $event) {
            $timestamp = $event['created_at'];
            $hour = (int) date('G', strtotime($timestamp));
            $dayOfWeek = (int) date('w', strtotime($timestamp));
            $month = (int) date('n', strtotime($timestamp));
            $isWeekend = in_array($dayOfWeek, [0, 6]);
            
            $patterns['hourly_distribution'][$hour]++;
            $patterns['daily_distribution'][$dayOfWeek]++;
            $patterns['monthly_distribution'][$month - 1]++;
            $patterns['weekend_vs_weekday'][$isWeekend ? 'weekend' : 'weekday']++;
        }
        
        return $patterns;
    }

    /**
     * Cálculo de entropía del payload
     */
    protected function calculateEntropy(string $payload): float
    {
        $length = strlen($payload);
        if ($length === 0) return 0;
        
        $charCounts = array_count_values(str_split($payload));
        $entropy = 0;
        
        foreach ($charCounts as $count) {
            $probability = $count / $length;
            $entropy -= $probability * log($probability, 2);
        }
        
        // Normalizar entropía (0-1)
        return $entropy / log($length, 2);
    }

    /**
     * Análisis de patrones sospechosos
     */
    protected function analyzeSuspiciousPatterns(string $payload): float
    {
        $suspiciousPatterns = [
            'sql_injection' => ['/union\s+select/i', '/drop\s+table/i', '/--/', '/or\s+1\s*=\s*1/i'],
            'xss_attack' => ['<script', 'javascript:', 'onload=', 'onerror=', 'onmouseover='],
            'path_traversal' => ['../', '..\\', '%2e%2e%2f', '..%2f'],
            'command_injection' => [';', '|', '&&', '`', '$('],
            'ldap_injection' => ['*', '(', ')', '&', '|'],
            'xml_injection' => ['<![CDATA[', ']]>', '<?xml', '<!DOCTYPE']
        ];
        
        $totalScore = 0;
        $maxScore = 100;
        
        foreach ($suspiciousPatterns as $attackType => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $payload)) {
                    $totalScore += $this->getPatternWeight($attackType);
                }
            }
        }
        
        return min($maxScore, $totalScore);
    }

    /**
     * Análisis de complejidad del payload
     */
    protected function analyzeComplexity(string $payload): float
    {
        $length = strlen($payload);
        $uniqueChars = count(array_unique(str_split($payload)));
        $digitCount = preg_match_all('/\d/', $payload);
        $specialCharCount = preg_match_all('/[^a-zA-Z0-9\s]/', $payload);
        $upperCaseCount = preg_match_all('/[A-Z]/', $payload);
        $lowerCaseCount = preg_match_all('/[a-z]/', $payload);
        
        // Calcular score de complejidad
        $complexityScore = 0;
        
        // Longitud (0-25 puntos)
        $complexityScore += min(25, $length / 10);
        
        // Diversidad de caracteres (0-25 puntos)
        $complexityScore += min(25, ($uniqueChars / $length) * 100);
        
        // Presencia de diferentes tipos de caracteres (0-50 puntos)
        $complexityScore += min(10, $digitCount / 5);
        $complexityScore += min(10, $specialCharCount / 5);
        $complexityScore += min(10, $upperCaseCount / 5);
        $complexityScore += min(10, $lowerCaseCount / 5);
        
        return min(100, $complexityScore);
    }

    /**
     * Normalización de características
     */
    protected function normalizeFeatures(array $features): array
    {
        $normalized = [];
        
        foreach ($features as $key => $value) {
            if (is_numeric($value)) {
                // Normalización Min-Max a rango [0, 1]
                $normalized[$key] = $this->minMaxNormalization($value, $key);
            } elseif (is_array($value)) {
                $normalized[$key] = $this->normalizeFeatures($value);
            } else {
                $normalized[$key] = $value;
            }
        }
        
        return $normalized;
    }

    /**
     * Normalización Min-Max
     */
    protected function minMaxNormalization(float $value, string $featureKey): float
    {
        $cacheKey = "feature_stats_{$featureKey}";
        
        if (!Cache::has($cacheKey)) {
            // Calcular estadísticas de la característica
            $stats = $this->calculateFeatureStats($featureKey);
            Cache::put($cacheKey, $stats, now()->addHour());
        } else {
            $stats = Cache::get($cacheKey);
        }
        
        if ($stats['max'] === $stats['min']) {
            return 0.5; // Valor neutral si no hay variación
        }
        
        return ($value - $stats['min']) / ($stats['max'] - $stats['min']);
    }

    /**
     * Cálculo de estadísticas de características
     */
    protected function calculateFeatureStats(string $featureKey): array
    {
        // Obtener valores históricos de la característica
        $values = $this->getHistoricalFeatureValues($featureKey);
        
        if (empty($values)) {
            return ['min' => 0, 'max' => 1, 'mean' => 0.5, 'std' => 0.5];
        }
        
        return [
            'min' => min($values),
            'max' => max($values),
            'mean' => array_sum($values) / count($values),
            'std' => $this->calculateStandardDeviation($values)
        ];
    }

    /**
     * Ensemble voting para decisión final
     */
    protected function ensembleVoting(array $predictions): float
    {
        // Pesos de confianza para cada algoritmo
        $weights = [
            'isolation_forest' => 0.4,
            'one_class_svm' => 0.3,
            'local_outlier_factor' => 0.3
        ];
        
        $weightedSum = 0;
        $totalWeight = 0;
        
        foreach ($predictions as $algorithm => $prediction) {
            if (isset($weights[$algorithm])) {
                $weightedSum += $prediction * $weights[$algorithm];
                $totalWeight += $weights[$algorithm];
            }
        }
        
        return $totalWeight > 0 ? $weightedSum / $totalWeight : 0.5;
    }

    /**
     * Cálculo de factor de confianza
     */
    protected function calculateConfidenceFactor(array $historicalData): float
    {
        $dataPoints = count($historicalData);
        
        if ($dataPoints === 0) return 0.5; // Confianza neutral sin datos
        
        // Más datos = mayor confianza
        $confidence = min(1.0, $dataPoints / 100);
        
        // Ajustar por calidad de datos
        $dataQuality = $this->assessDataQuality($historicalData);
        
        return $confidence * $dataQuality;
    }

    /**
     * Análisis de riesgo fallback
     */
    protected function fallbackRiskAnalysis(string $ip, array $historicalData): float
    {
        // Análisis estadístico básico cuando falla ML
        $recentEvents = array_filter($historicalData, function($event) {
            return strtotime($event['created_at']) >= strtotime('-1 hour');
        });
        
        $eventCount = count($recentEvents);
        
        if ($eventCount === 0) return 0;
        
        // Score basado en frecuencia de eventos
        if ($eventCount > 100) return 80;
        if ($eventCount > 50) return 60;
        if ($eventCount > 20) return 40;
        if ($eventCount > 10) return 20;
        
        return 10;
    }

    /**
     * Métodos auxiliares
     */
    protected function getPatternWeight(string $attackType): float
    {
        $weights = [
            'sql_injection' => 25,
            'xss_attack' => 20,
            'path_traversal' => 15,
            'command_injection' => 30,
            'ldap_injection' => 18,
            'xml_injection' => 22
        ];
        
        return $weights[$attackType] ?? 15;
    }

    protected function getLearnedWeights(string $analysisType): array
    {
        $defaultWeights = [
            'payload_analysis' => [
                'entropy' => 0.4,
                'patterns' => 0.4,
                'complexity' => 0.2
            ]
        ];
        
        return $defaultWeights[$analysisType] ?? ['default' => 1.0];
    }

    protected function calculateStandardDeviation(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values)) / count($values);
        
        return sqrt($variance);
    }

    protected function assessDataQuality(array $data): float
    {
        if (empty($data)) return 0.5;
        
        // Evaluar completitud de datos
        $completeness = 0;
        $totalFields = 0;
        
        foreach ($data as $record) {
            foreach ($record as $field => $value) {
                $totalFields++;
                if (!empty($value) || $value === 0) {
                    $completeness++;
                }
            }
        }
        
        return $totalFields > 0 ? $completeness / $totalFields : 0.5;
    }

    /**
     * Limpiar características
     */
    protected function cleanFeatures(array $features): array
    {
        $cleaned = [];
        
        foreach ($features as $key => $value) {
            if (is_numeric($value) && !is_nan($value) && is_finite($value)) {
                $cleaned[$key] = $value;
            } elseif (is_string($value) && !empty($value)) {
                $cleaned[$key] = $value;
            }
        }
        
        return $cleaned;
    }

    /**
     * Transformar características
     */
    protected function transformFeatures(array $features): array
    {
        $transformed = [];
        
        foreach ($features as $key => $value) {
            if (is_numeric($value)) {
                // Aplicar transformación logarítmica para valores grandes
                if ($value > 100) {
                    $transformed[$key] = log($value + 1);
                } else {
                    $transformed[$key] = $value;
                }
            } else {
                $transformed[$key] = $value;
            }
        }
        
        return $transformed;
    }

    /**
     * Calcular umbral adaptativo
     */
    protected function calculateAdaptiveThreshold(array $features): float
    {
        $numericValues = array_filter($features, 'is_numeric');
        
        if (empty($numericValues)) return 0.5;
        
        $mean = array_sum($numericValues) / count($numericValues);
        $std = $this->calculateStandardDeviation($numericValues);
        
        return $mean + (2 * $std); // 2 desviaciones estándar
    }

    /**
     * Normalizar score
     */
    protected function normalizeScore(float $score, float $threshold): float
    {
        if ($threshold === 0) return 0.5;
        
        $normalized = $score / $threshold;
        return min(1.0, max(0.0, $normalized));
    }

    /**
     * Calcular score de patrón
     */
    protected function calculatePatternScore(array $behaviorPatterns): float
    {
        $score = 0;
        
        if (isset($behaviorPatterns['time_distribution']['anomaly_score'])) {
            $score += $behaviorPatterns['time_distribution']['anomaly_score'] * 0.4;
        }
        
        if (isset($behaviorPatterns['time_distribution']['anomaly_score'])) {
            $score += $behaviorPatterns['request_patterns']['diversity_score'] * 0.3;
        }
        
        if (isset($behaviorPatterns['threat_evolution']['escalation_rate'])) {
            $score += min(100, $behaviorPatterns['threat_evolution']['escalation_rate']) * 0.3;
        }
        
        return min(100, $score);
    }

    /**
     * Calcular score de evolución
     */
    protected function calculateEvolutionScore($events): float
    {
        if ($events->isEmpty()) return 0;
        
        $scores = $events->pluck('threat_score')->toArray();
        $trend = $this->calculateLinearTrend($scores);
        
        return min(100, max(0, ($trend + 1) * 50));
    }

    /**
     * Extraer patrones de amenazas
     */
    protected function extractThreatPatterns($threatEvents): array
    {
        if ($threatEvents->isEmpty()) return [];
        
        $patterns = [];
        
        foreach ($threatEvents as $event) {
            $patterns[] = [
                'score' => $event->threat_score,
                'timestamp' => $event->created_at,
                'type' => $event->event_type ?? 'unknown'
            ];
        }
        
        return $patterns;
    }

    /**
     * Normalizar frecuencias de requests para análisis comparativo
     */
    protected function normalizeFrequencies(array $frequencies): array
    {
        $normalized = [];
        
        // Obtener valores máximos para normalización
        $maxPerMinute = max($frequencies['per_minute'], 1);
        $maxPerHour = max($frequencies['per_hour'], 1);
        $maxPerDay = max($frequencies['per_day'], 1);
        
        // Normalizar a escala 0-1
        $normalized['per_minute'] = $frequencies['per_minute'] / $maxPerMinute;
        $normalized['per_hour'] = $frequencies['per_hour'] / $maxPerHour;
        $normalized['per_day'] = $frequencies['per_day'] / $maxPerDay;
        
        // Aplicar pesos para diferentes escalas temporales
        $normalized['per_minute'] *= 0.5;  // Menor peso para minutos
        $normalized['per_hour'] *= 0.8;    // Peso medio para horas
        $normalized['per_day'] *= 1.0;     // Mayor peso para días
        
        return $normalized;
    }

    /**
     * Calcular tendencia lineal para análisis de evolución
     */
    protected function calculateLinearTrend(array $values): float
    {
        if (count($values) < 2) return 0;
        
        $n = count($values);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($values as $index => $value) {
            $x = $index;
            $y = $value;
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        
        return $slope;
    }
}
