<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;

class OneClassSVM
{
    protected $config;
    protected $supportVectors = [];
    protected $isTrained = false;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'nu' => 0.1,
            'kernel' => 'rbf',
            'gamma' => 'scale',
            'random_state' => null
        ], $config);
    }

    /**
     * Predecir anomalía usando One-Class SVM
     */
    public function predict(array $features): float
    {
        try {
            if (!$this->isTrained) {
                // Si no está entrenado, usar predicción por defecto
                return $this->defaultPrediction($features);
            }
            
            // Normalizar características
            $normalizedFeatures = $this->normalizeFeatures($features);
            
            // Calcular distancia al hiperplano de decisión
            $decisionScore = $this->calculateDecisionScore($normalizedFeatures);
            
            // Convertir a probabilidad de anomalía
            $anomalyProbability = $this->convertToAnomalyProbability($decisionScore);
            
            return $anomalyProbability;
            
        } catch (\Exception $e) {
            Log::error('One-Class SVM Prediction Error', [
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
                        if ($value > 500) $anomalyIndicators++;
                        break;
                    case 'threat_score':
                        if ($value > 70) $anomalyIndicators++;
                        break;
                    case 'anomaly_score':
                        if ($value > 80) $anomalyIndicators++;
                        break;
                    case 'consistency_score':
                        if ($value < 30) $anomalyIndicators++;
                        break;
                    case 'geographic_velocity':
                        if ($value > 80) $anomalyIndicators++;
                        break;
                    case 'night_activity':
                        if ($value > 70) $anomalyIndicators++;
                        break;
                    case 'attack_indicators':
                        if ($value > 60) $anomalyIndicators++;
                        break;
                }
            }
        }
        
        if ($totalIndicators === 0) return 0.5;
        
        $anomalyRatio = $anomalyIndicators / $totalIndicators;
        return min(1.0, $anomalyRatio * 2);
    }

    /**
     * Calcular score de decisión
     */
    protected function calculateDecisionScore(array $features): float
    {
        $score = 0;
        
        // Simulación de cálculo de score usando kernel RBF
        foreach ($this->supportVectors as $vector) {
            $kernelValue = $this->calculateKernel($features, $vector);
            $score += $kernelValue * $vector['alpha'];
        }
        
        // Agregar bias
        $score += $this->config['nu'];
        
        return $score;
    }

    /**
     * Calcular valor del kernel
     */
    protected function calculateKernel(array $x1, array $x2): float
    {
        switch ($this->config['kernel']) {
            case 'rbf':
                return $this->rbfKernel($x1, $x2);
            case 'linear':
                return $this->linearKernel($x1, $x2);
            case 'poly':
                return $this->polyKernel($x1, $x2);
            default:
                return $this->rbfKernel($x1, $x2);
        }
    }

    /**
     * Kernel RBF (Radial Basis Function)
     */
    protected function rbfKernel(array $x1, array $x2): float
    {
        $gamma = $this->getGamma();
        
        $squaredDistance = 0;
        foreach ($x1 as $key => $value1) {
            if (isset($x2[$key])) {
                $diff = $value1 - $x2[$key];
                $squaredDistance += $diff * $diff;
            }
        }
        
        return exp(-$gamma * $squaredDistance);
    }

    /**
     * Kernel lineal
     */
    protected function linearKernel(array $x1, array $x2): float
    {
        $dotProduct = 0;
        foreach ($x1 as $key => $value1) {
            if (isset($x2[$key])) {
                $dotProduct += $value1 * $x2[$key];
            }
        }
        
        return $dotProduct;
    }

    /**
     * Kernel polinomial
     */
    protected function polyKernel(array $x1, array $x2): float
    {
        $linearKernel = $this->linearKernel($x1, $x2);
        return pow($linearKernel + 1, 3); // Polinomio de grado 3
    }

    /**
     * Obtener valor de gamma
     */
    protected function getGamma(): float
    {
        if ($this->config['gamma'] === 'scale') {
            return 1.0 / 10; // Valor por defecto para características normalizadas
        } elseif ($this->config['gamma'] === 'auto') {
            return 1.0 / 10;
        } else {
            return (float) $this->config['gamma'];
        }
    }

    /**
     * Convertir score de decisión a probabilidad de anomalía
     */
    protected function convertToAnomalyProbability(float $decisionScore): float
    {
        // Scores negativos indican anomalías
        if ($decisionScore < 0) {
            // Convertir a probabilidad usando función sigmoidal
            $sigmoid = 1 / (1 + exp(-$decisionScore));
            return min(1.0, $sigmoid * 2);
        } else {
            // Scores positivos indican comportamiento normal
            $sigmoid = 1 / (1 + exp($decisionScore));
            return max(0.0, 1 - $sigmoid);
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
            // Simulación de entrenamiento
            $this->supportVectors = [];
            
            // Crear vectores de soporte simulados
            $numVectors = min(10, count($trainingData));
            
            for ($i = 0; $i < $numVectors; $i++) {
                $this->supportVectors[] = [
                    'features' => $trainingData[$i] ?? [],
                    'alpha' => rand(1, 10) / 10, // Coeficiente de Lagrange
                    'bias' => rand(-5, 5) / 10
                ];
            }
            
            $this->isTrained = true;
            
            Log::info('One-Class SVM training completed', [
                'support_vectors' => count($this->supportVectors)
            ]);
            
        } catch (\Exception $e) {
            Log::error('One-Class SVM training error', [
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
