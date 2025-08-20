<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;

class IsolationForest
{
    protected $config;
    protected $trees = [];
    protected $maxDepth;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'n_estimators' => 100,
            'max_samples' => 'auto',
            'contamination' => 0.1,
            'random_state' => null
        ], $config);
        
        $this->maxDepth = ceil(log($this->config['n_estimators'], 2));
    }

    /**
     * Predecir anomalía usando Isolation Forest
     */
    public function predict(array $features): float
    {
        try {
            // Normalizar características
            $normalizedFeatures = $this->normalizeFeatures($features);
            
            // Calcular scores de anomalía usando múltiples árboles
            $anomalyScores = [];
            
            for ($i = 0; $i < $this->config['n_estimators']; $i++) {
                $tree = $this->buildIsolationTree($normalizedFeatures);
                $score = $this->calculatePathLength($normalizedFeatures, $tree);
                $anomalyScores[] = $score;
            }
            
            // Calcular score final promedio
            $averageScore = array_sum($anomalyScores) / count($anomalyScores);
            
            // Normalizar score a rango [0, 1]
            $normalizedScore = $this->normalizeScore($averageScore);
            
            return $normalizedScore;
            
        } catch (\Exception $e) {
            Log::error('Isolation Forest Prediction Error', [
                'error' => $e->getMessage()
            ]);
            
            return 0.5; // Score neutral en caso de error
        }
    }

    /**
     * Construir árbol de aislamiento
     */
    protected function buildIsolationTree(array $features): array
    {
        $tree = [
            'type' => 'leaf',
            'size' => count($features),
            'depth' => 0
        ];
        
        if (count($features) <= 1 || $tree['depth'] >= $this->maxDepth) {
            return $tree;
        }
        
        // Seleccionar característica aleatoria para dividir
        $featureKeys = array_keys($features);
        $splitFeature = $featureKeys[array_rand($featureKeys)];
        $splitValue = $features[$splitFeature];
        
        // Dividir datos
        $leftFeatures = [];
        $rightFeatures = [];
        
        foreach ($features as $key => $value) {
            if ($value <= $splitValue) {
                $leftFeatures[$key] = $value;
            } else {
                $rightFeatures[$key] = $value;
            }
        }
        
        // Crear nodos hijos
        $tree['type'] = 'split';
        $tree['feature'] = $splitFeature;
        $tree['value'] = $splitValue;
        $tree['left'] = $this->buildIsolationTree($leftFeatures);
        $tree['right'] = $this->buildIsolationTree($rightFeatures);
        
        return $tree;
    }

    /**
     * Calcular longitud de camino para una característica
     */
    protected function calculatePathLength(array $features, array $tree): float
    {
        if ($tree['type'] === 'leaf') {
            return $this->calculateExpectedPathLength($tree['size']);
        }
        
        $feature = $tree['feature'];
        $value = $tree['value'];
        
        if (isset($features[$feature]) && $features[$feature] <= $value) {
            return 1 + $this->calculatePathLength($features, $tree['left']);
        } else {
            return 1 + $this->calculatePathLength($features, $tree['right']);
        }
    }

    /**
     * Calcular longitud de camino esperada
     */
    protected function calculateExpectedPathLength(int $size): float
    {
        if ($size <= 1) return 0;
        
        // Aproximación de la longitud de camino esperada
        return 2 * (log($size - 1) + 0.5772156649) - (2 * ($size - 1) / $size);
    }

    /**
     * Normalizar características
     */
    protected function normalizeFeatures(array $features): array
    {
        $normalized = [];
        
        foreach ($features as $key => $value) {
            if (is_numeric($value)) {
                // Normalización Min-Max a rango [0, 1]
                $normalized[$key] = $this->minMaxNormalization($value, $key);
            } else {
                $normalized[$key] = 0.5; // Valor neutral para características no numéricas
            }
        }
        
        return $normalized;
    }

    /**
     * Normalización Min-Max
     */
    protected function minMaxNormalization(float $value, string $featureKey): float
    {
        // Valores de rango predefinidos para características comunes
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
     * Normalizar score final
     */
    protected function normalizeScore(float $score): float
    {
        // Convertir score de Isolation Forest a probabilidad de anomalía
        // Scores más bajos indican mayor probabilidad de anomalía
        
        // Aplicar transformación sigmoidal
        $sigmoid = 1 / (1 + exp($score - 5));
        
        // Ajustar por contaminación esperada
        $adjustedScore = $sigmoid * $this->config['contamination'];
        
        return min(1.0, max(0.0, $adjustedScore));
    }

    /**
     * Entrenar el modelo (placeholder para implementación futura)
     */
    public function fit(array $trainingData): void
    {
        // En una implementación real, aquí se entrenarían los árboles
        // Por ahora, solo registramos que se llamó al método
        Log::info('Isolation Forest training called', [
            'training_samples' => count($trainingData)
        ]);
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
