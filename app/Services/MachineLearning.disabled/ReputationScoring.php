<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ReputationScoring
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'weights' => [
                'historical' => 0.30,
                'geographic' => 0.20,
                'network' => 0.25,
                'threat' => 0.25
            ],
            'score_range' => [0, 100],
            'confidence_threshold' => 0.7
        ];
    }

    /**
     * Calcular score de reputación usando múltiples factores
     */
    public function calculateScore(array $factors): float
    {
        try {
            $totalScore = 0;
            $totalWeight = 0;
            
            foreach ($this->config['weights'] as $factor => $weight) {
                if (isset($factors[$factor])) {
                    $factorScore = $this->normalizeFactorScore($factors[$factor], $factor);
                    $totalScore += $factorScore * $weight;
                    $totalWeight += $weight;
                }
            }
            
            if ($totalWeight === 0) return 50; // Score neutral
            
            $finalScore = $totalScore / $totalWeight;
            
            // Aplicar factor de confianza
            $confidenceFactor = $this->calculateConfidenceFactor($factors);
            $finalScore *= $confidenceFactor;
            
            return min(100, max(0, $finalScore));
            
        } catch (\Exception $e) {
            Log::error('Reputation Scoring Error', [
                'error' => $e->getMessage()
            ]);
            
            return 50; // Score neutral en caso de error
        }
    }

    /**
     * Normalizar score de factor específico
     */
    protected function normalizeFactorScore($factorData, string $factorType): float
    {
        switch ($factorType) {
            case 'historical':
                return $this->normalizeHistoricalScore($factorData);
            case 'geographic':
                return $this->normalizeGeographicScore($factorData);
            case 'network':
                return $this->normalizeNetworkScore($factorData);
            case 'threat':
                return $this->normalizeThreatScore($factorData);
            default:
                return 50;
        }
    }

    /**
     * Normalizar score histórico
     */
    protected function normalizeHistoricalScore($data): float
    {
        if (!is_array($data)) return 50;
        
        $score = 0;
        
        if (isset($data['total_events'])) {
            $totalEvents = $data['total_events'];
            if ($totalEvents > 0) {
                $threatEvents = $data['threat_events'] ?? 0;
                $threatRatio = $threatEvents / $totalEvents;
                $score += $threatRatio * 40;
            }
        }
        
        if (isset($data['threat_frequency'])) {
            $score += $data['threat_frequency'] * 30;
        }
        
        if (isset($data['score'])) {
            $score += $data['score'] * 0.3;
        }
        
        return min(100, $score);
    }

    /**
     * Normalizar score geográfico
     */
    protected function normalizeGeographicScore($data): float
    {
        if (!is_array($data)) return 50;
        
        $score = 50; // Score base neutral
        
        if (isset($data['risk_level'])) {
            switch ($data['risk_level']) {
                case 'critical':
                    $score = 90;
                    break;
                case 'high':
                    $score = 75;
                    break;
                case 'medium':
                    $score = 60;
                    break;
                case 'low':
                    $score = 30;
                    break;
                case 'minimal':
                    $score = 10;
                    break;
            }
        }
        
        if (isset($data['confidence'])) {
            $confidence = $data['confidence'] / 100;
            $score = $score * $confidence + 50 * (1 - $confidence);
        }
        
        return $score;
    }

    /**
     * Normalizar score de red
     */
    protected function normalizeNetworkScore($data): float
    {
        if (!is_array($data)) return 50;
        
        $score = 50;
        
        if (isset($data['network_reputation'])) {
            switch ($data['network_reputation']) {
                case 'malicious':
                    $score = 90;
                    break;
                case 'suspicious':
                    $score = 70;
                    break;
                case 'neutral':
                    $score = 50;
                    break;
                case 'trusted':
                    $score = 20;
                    break;
                case 'verified':
                    $score = 10;
                    break;
            }
        }
        
        if (isset($data['score'])) {
            $score = ($score + $data['score']) / 2;
        }
        
        return $score;
    }

    /**
     * Normalizar score de amenaza
     */
    protected function normalizeThreatScore($data): float
    {
        if (!is_array($data)) return 50;
        
        $score = 50;
        
        if (isset($data['threat_score'])) {
            $score = $data['threat_score'];
        }
        
        if (isset($data['classification'])) {
            $classification = $data['classification'];
            if (is_array($classification) && isset($classification['level'])) {
                $level = $classification['level'];
                switch ($level) {
                    case 'critical':
                        $score = max($score, 90);
                        break;
                    case 'high':
                        $score = max($score, 75);
                        break;
                    case 'medium':
                        $score = max($score, 60);
                        break;
                    case 'low':
                        $score = max($score, 40);
                        break;
                }
            }
        }
        
        return $score;
    }

    /**
     * Calcular factor de confianza
     */
    protected function calculateConfidenceFactor(array $factors): float
    {
        $confidenceScores = [];
        
        foreach ($factors as $factor => $data) {
            if (is_array($data) && isset($data['confidence'])) {
                $confidenceScores[] = $data['confidence'] / 100;
            }
        }
        
        if (empty($confidenceScores)) return 0.8; // Confianza por defecto
        
        $averageConfidence = array_sum($confidenceScores) / count($confidenceScores);
        
        return min(1.0, max(0.5, $averageConfidence));
    }
}
