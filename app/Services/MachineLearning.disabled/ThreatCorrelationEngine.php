<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ThreatCorrelationEngine
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'correlation_threshold' => 0.7,
            'time_window' => 3600, // 1 hora
            'max_correlations' => 100
        ];
    }

    /**
     * Correlacionar amenazas usando análisis de patrones
     */
    public function correlateThreats(array $threatData): float
    {
        try {
            // Análisis de correlación temporal
            $temporalCorrelation = $this->analyzeTemporalCorrelation($threatData);
            
            // Análisis de correlación geográfica
            $geographicCorrelation = $this->analyzeGeographicCorrelation($threatData);
            
            // Análisis de correlación de comportamiento
            $behavioralCorrelation = $this->analyzeBehavioralCorrelation($threatData);
            
            // Análisis de correlación de patrones
            $patternCorrelation = $this->analyzePatternCorrelation($threatData);
            
            // Score de correlación final
            $correlationScore = ($temporalCorrelation + $geographicCorrelation + $behavioralCorrelation + $patternCorrelation) / 4;
            
            return min(1.0, max(0.0, $correlationScore));
            
        } catch (\Exception $e) {
            Log::error('Threat Correlation Error', [
                'error' => $e->getMessage()
            ]);
            
            return 0.5; // Score neutral en caso de error
        }
    }

    /**
     * Análisis de correlación temporal
     */
    protected function analyzeTemporalCorrelation(array $threatData): float
    {
        $timePatterns = [];
        $totalThreats = 0;
        
        foreach ($threatData as $source => $data) {
            if (isset($data['timestamp']) || isset($data['last_updated'])) {
                $timestamp = $data['timestamp'] ?? $data['last_updated'];
                $hour = (int) date('G', strtotime($timestamp));
                $timePatterns[$hour] = ($timePatterns[$hour] ?? 0) + 1;
                $totalThreats++;
            }
        }
        
        if ($totalThreats === 0) return 0.5;
        
        // Calcular concentración temporal
        $maxConcentration = max($timePatterns);
        $temporalScore = $maxConcentration / $totalThreats;
        
        return min(1.0, $temporalScore * 2);
    }

    /**
     * Análisis de correlación geográfica
     */
    protected function analyzeGeographicCorrelation(array $threatData): float
    {
        $geographicPatterns = [];
        $totalThreats = 0;
        
        foreach ($threatData as $source => $data) {
            if (isset($data['country_code']) || isset($data['country'])) {
                $country = $data['country_code'] ?? $data['country'];
                $geographicPatterns[$country] = ($geographicPatterns[$country] ?? 0) + 1;
                $totalThreats++;
            }
        }
        
        if ($totalThreats === 0) return 0.5;
        
        // Calcular concentración geográfica
        $maxConcentration = max($geographicPatterns);
        $geographicScore = $maxConcentration / $totalThreats;
        
        return min(1.0, $geographicScore * 2);
    }

    /**
     * Análisis de correlación de comportamiento
     */
    protected function analyzeBehavioralCorrelation(array $threatData): float
    {
        $behavioralPatterns = [];
        $totalThreats = 0;
        
        foreach ($threatData as $source => $data) {
            if (isset($data['score'])) {
                $score = $data['score'];
                $behavioralPatterns[] = $score;
                $totalThreats++;
            }
        }
        
        if ($totalThreats === 0) return 0.5;
        
        // Calcular consistencia de comportamiento
        $meanScore = array_sum($behavioralPatterns) / $totalThreats;
        $variance = array_sum(array_map(function($score) use ($meanScore) {
            return pow($score - $meanScore, 2);
        }, $behavioralPatterns)) / $totalThreats;
        
        $stdDev = sqrt($variance);
        $consistencyScore = 1.0 - min(1.0, $stdDev / 100);
        
        return $consistencyScore;
    }

    /**
     * Análisis de correlación de patrones
     */
    protected function analyzePatternCorrelation(array $threatData): float
    {
        $patterns = [];
        $totalThreats = 0;
        
        foreach ($threatData as $source => $data) {
            $pattern = $this->extractThreatPattern($data);
            if ($pattern) {
                $patterns[$pattern] = ($patterns[$pattern] ?? 0) + 1;
                $totalThreats++;
            }
        }
        
        if ($totalThreats === 0) return 0.5;
        
        // Calcular diversidad de patrones
        $uniquePatterns = count($patterns);
        $diversityScore = $uniquePatterns / $totalThreats;
        
        // Score inverso: menos diversidad = mayor correlación
        return 1.0 - min(1.0, $diversityScore);
    }

    /**
     * Extraer patrón de amenaza de los datos
     */
    protected function extractThreatPattern(array $data): ?string
    {
        if (isset($data['proxy']) && $data['proxy']) return 'proxy';
        if (isset($data['vpn']) && $data['vpn']) return 'vpn';
        if (isset($data['tor']) && $data['tor']) return 'tor';
        if (isset($data['bot']) && $data['bot']) return 'bot';
        if (isset($data['abuse_confidence']) && $data['abuse_confidence'] > 50) return 'abuse';
        if (isset($data['detected_urls']) && $data['detected_urls'] > 0) return 'malware';
        
        return null;
    }
}
