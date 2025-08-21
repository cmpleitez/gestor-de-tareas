<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;

class LocalOutlierFactor
{
    protected $config;
    protected $trainingData = [];
    protected $isTrained = false;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'n_neighbors' => 20,
            'contamination' => 0.1,
            'metric' => 'euclidean',
            'algorithm' => 'auto'
        ], $config);
    }

    /**
     * Predecir anomalía usando Local Outlier Factor
     */
    public function predict(array $features): float
    {
        try {
            if (!$this->isTrained || empty($this->trainingData)) {
                // Si no está entrenado, usar predicción por defecto
                return $this->defaultPrediction($features);
            }
            
            // Normalizar características
            $normalizedFeatures = $this->normalizeFeatures($features);
            
            // Calcular LOF score
            $lofScore = $this->calculateLOF($normalizedFeatures);
            
            // Convertir a probabilidad de anomalía
            $anomalyProbability = $this->convertToAnomalyProbability($lofScore);
            
            return $anomalyProbability;
            
        } catch (\Exception $e) {
            Log::error('Local Outlier Factor Prediction Error', [
                'error' => $e->getMessage()
            ]);
            
            return 0.5; // Score neutral en caso de error
        }
    }

    /**
     * Predicción por defecto cuando el modelo no está entrenado
     */
    protected function defaultPrediction(array $features): float
    {
        // Análisis heurístico simple basado en características
        $anomalyIndicators = 0;
        $totalIndicators = 0;
        
        foreach ($features as $key => $value) {
            if (is_numeric($value)) {
                $totalIndicators++;
                
                // Detectar valores anómalos basados en umbrales
                switch ($key) {
                    case 'request_frequency':
                        if ($value > 600) $anomalyIndicators++;
                        break;
                    case 'threat_score':
                        if ($value > 75) $anomalyIndicators++;
                        break;
                    case 'anomaly_score':
                        if ($value > 85) $anomalyIndicators++;
                        break;
                    case 'consistency_score':
                        if ($value < 25) $anomalyIndicators++;
                        break;
                    case 'geographic_velocity':
                        if ($value > 85) $anomalyIndicators++;
                        break;
                    case 'night_activity':
                        if ($value > 75) $anomalyIndicators++;
                        break;
                    case 'attack_indicators':
                        if ($value > 65) $anomalyIndicators++;
                        break;
                }
            }
        }
        
        if ($totalIndicators === 0) return 0.5;
        
        $anomalyRatio = $anomalyIndicators / $totalIndicators;
        return min(1.0, $anomalyRatio * 2);
    }

    /**
     * Calcular Local Outlier Factor
     */
    protected function calculateLOF(array $features): float
    {
        // Encontrar k vecinos más cercanos
        $neighbors = $this->findKNearestNeighbors($features);
        
        if (empty($neighbors)) return 1.0;
        
        // Calcular reachability distance
        $reachabilityDistances = [];
        foreach ($neighbors as $neighbor) {
            $reachabilityDistances[] = $this->calculateReachabilityDistance($features, $neighbor);
        }
        
        // Calcular local reachability density
        $localReachabilityDensity = $this->calculateLocalReachabilityDensity($features, $neighbors);
        
        // Calcular LOF score
        $lofScore = $this->calculateLOFScore($features, $neighbors, $localReachabilityDensity);
        
        return $lofScore;
    }

    /**
     * Encontrar k vecinos más cercanos
     */
    protected function findKNearestNeighbors(array $features): array
    {
        $distances = [];
        
        foreach ($this->trainingData as $index => $trainingPoint) {
            $distance = $this->calculateDistance($features, $trainingPoint);
            $distances[] = [
                'index' => $index,
                'distance' => $distance,
                'features' => $trainingPoint
            ];
        }
        
        // Ordenar por distancia
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        // Tomar solo los k primeros
        $k = min($this->config['n_neighbors'], count($distances));
        return array_slice($distances, 0, $k);
    }

    /**
     * Calcular distancia entre dos puntos
     */
    protected function calculateDistance(array $point1, array $point2): float
    {
        switch ($this->config['metric']) {
            case 'euclidean':
                return $this->euclideanDistance($point1, $point2);
            case 'manhattan':
                return $this->manhattanDistance($point1, $point2);
            case 'cosine':
                return $this->cosineDistance($point1, $point2);
            default:
                return $this->euclideanDistance($point1, $point2);
        }
    }

    /**
     * Distancia euclidiana
     */
    protected function euclideanDistance(array $point1, array $point2): float
    {
        $sumSquared = 0;
        
        foreach ($point1 as $key => $value1) {
            if (isset($point2[$key])) {
                $diff = $value1 - $point2[$key];
                $sumSquared += $diff * $diff;
            }
        }
        
        return sqrt($sumSquared);
    }

    /**
     * Distancia Manhattan
     */
    protected function manhattanDistance(array $point1, array $point2): float
    {
        $sum = 0;
        
        foreach ($point1 as $key => $value1) {
            if (isset($point2[$key])) {
                $sum += abs($value1 - $point2[$key]);
            }
        }
        
        return $sum;
    }

    /**
     * Distancia coseno
     */
    protected function cosineDistance(array $point1, array $point2): float
    {
        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;
        
        foreach ($point1 as $key => $value1) {
            if (isset($point2[$key])) {
                $dotProduct += $value1 * $point2[$key];
                $norm1 += $value1 * $value1;
                $norm2 += $point2[$key] * $point2[$key];
            }
        }
        
        if ($norm1 == 0 || $norm2 == 0) return 1.0;
        
        $cosine = $dotProduct / (sqrt($norm1) * sqrt($norm2));
        return 1 - $cosine; // Convertir similitud a distancia
    }

    /**
     * Calcular reachability distance
     */
    protected function calculateReachabilityDistance(array $point1, array $neighbor): float
    {
        $distance = $neighbor['distance'];
        $kDistance = $this->calculateKDistance($neighbor['features']);
        
        return max($distance, $kDistance);
    }

    /**
     * Calcular k-distance
     */
    protected function calculateKDistance(array $point): float
    {
        $distances = [];
        
        foreach ($this->trainingData as $trainingPoint) {
            if ($trainingPoint !== $point) {
                $distances[] = $this->calculateDistance($point, $trainingPoint);
            }
        }
        
        if (empty($distances)) return 0;
        
        sort($distances);
        $k = min($this->config['n_neighbors'], count($distances));
        
        return $distances[$k - 1] ?? 0;
    }

    /**
     * Calcular local reachability density
     */
    protected function calculateLocalReachabilityDensity(array $features, array $neighbors): float
    {
        if (empty($neighbors)) return 1.0;
        
        $sumReachability = 0;
        
        foreach ($neighbors as $neighbor) {
            $sumReachability += $neighbor['distance'];
        }
        
        $averageReachability = $sumReachability / count($neighbors);
        
        if ($averageReachability == 0) return 1.0;
        
        return 1.0 / $averageReachability;
    }

    /**
     * Calcular LOF score final
     */
    protected function calculateLOFScore(array $features, array $neighbors, float $localReachabilityDensity): float
    {
        if (empty($neighbors) || $localReachabilityDensity == 0) return 1.0;
        
        $sumRatios = 0;
        
        foreach ($neighbors as $neighbor) {
            $neighborDensity = $this->calculateLocalReachabilityDensity($neighbor['features'], $neighbors);
            if ($neighborDensity > 0) {
                $sumRatios += $neighborDensity / $localReachabilityDensity;
            }
        }
        
        $averageRatio = $sumRatios / count($neighbors);
        
        return $averageRatio;
    }

    /**
     * Convertir LOF score a probabilidad de anomalía
     */
    protected function convertToAnomalyProbability(float $lofScore): float
    {
        // LOF > 1 indica anomalía
        if ($lofScore > 1.0) {
            // Normalizar usando función sigmoidal
            $normalizedScore = ($lofScore - 1.0) / 2.0; // Normalizar a rango [0, 1]
            $sigmoid = 1 / (1 + exp(-$normalizedScore * 5));
            return min(1.0, $sigmoid);
        } else {
            // LOF <= 1 indica comportamiento normal
            return max(0.0, 1.0 - $lofScore);
        }
    }

    /**
     * Normalizar características
     */
    protected function normalizeFeatures(array $features): array
    {
        $normalized = [];
        
        foreach ($features as $key => $value) {
            if (is_numeric($value)) {
                $normalized[$key] = $this->minMaxNormalization($value, $key);
            } else {
                $normalized[$key] = 0.5;
            }
        }
        
        return $normalized;
    }

    /**
     * Normalización Min-Max
     */
    protected function minMaxNormalization(float $value, string $featureKey): float
    {
        $ranges = [
            'request_frequency' => [0, 1000],
            'threat_score' => [0, 100],
            'anomaly_score' => [0, 100],
            'consistency_score' => [0, 100],
            'geographic_velocity' => [0, 100],
            'night_activity' => [0, 100],
            'attack_indicators' => [0, 100]
        ];
        
        $range = $ranges[$featureKey] ?? [0, 100];
        $min = $range[0];
        $max = $range[1];
        
        if ($max === $min) return 0.5;
        
        return ($value - $min) / ($max - $min);
    }

    /**
     * Entrenar el modelo
     */
    public function fit(array $trainingData): void
    {
        try {
            $this->trainingData = [];
            
            // Normalizar datos de entrenamiento
            foreach ($trainingData as $dataPoint) {
                $this->trainingData[] = $this->normalizeFeatures($dataPoint);
            }
            
            $this->isTrained = true;
            
            Log::info('Local Outlier Factor training completed', [
                'training_samples' => count($this->trainingData)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Local Outlier Factor training error', [
                'error' => $e->getMessage()
            ]);
            
            $this->isTrained = false;
        }
    }

    /**
     * Verificar si el modelo está entrenado
     */
    public function isTrained(): bool
    {
        return $this->isTrained;
    }

    /**
     * Obtener configuración del modelo
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Establecer configuración del modelo
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }
}
