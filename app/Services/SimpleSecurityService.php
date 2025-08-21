<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\SecurityEvent;

class SimpleSecurityService
{
    protected $config = [
        'cache_duration' => 3600, // 1 hora
        'max_requests_per_hour' => 100,
        'critical_threat_threshold' => 80,
    ];

    /**
     * Verificación simple de riesgo de IP
     */
    public function checkIPRisk(string $ip): float
    {
        $cacheKey = "ip_risk_{$ip}";
        
        // Verificar cache primero
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Solo verificar eventos críticos de la última hora
        $criticalEvents = SecurityEvent::where('ip_address', $ip)
            ->where('threat_score', '>=', 80)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        $riskScore = min(100, $criticalEvents * 25);
        
        // Cache por 1 hora
        Cache::put($cacheKey, $riskScore, now()->addHour());
        
        return $riskScore;
    }

    /**
     * Verificación simple de frecuencia de requests
     */
    public function checkRequestFrequency(string $ip): float
    {
        $cacheKey = "request_freq_{$ip}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Solo contar requests de la última hora
        $requestCount = SecurityEvent::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        $frequencyScore = min(100, $requestCount);
        
        Cache::put($cacheKey, $frequencyScore, now()->addHour());
        
        return $frequencyScore;
    }

    /**
     * Análisis simple de amenazas
     */
    public function analyzeThreats(string $ip): array
    {
        $riskScore = $this->checkIPRisk($ip);
        $frequencyScore = $this->checkRequestFrequency($ip);
        
        $totalScore = max($riskScore, $frequencyScore);
        
        return [
            'total_score' => $totalScore,
            'risk_level' => $this->categorizeRisk($totalScore),
            'ip_address' => $ip,
            'timestamp' => now(),
            'risk_score' => $riskScore,
            'frequency_score' => $frequencyScore
        ];
    }

    /**
     * Categorización simple de riesgo
     */
    protected function categorizeRisk(float $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'minimal';
    }

    /**
     * Limpieza de cache expirado
     */
    public function cleanupExpiredCache(): void
    {
        // Limpiar cache expirado cada hora
        $expiredKeys = Cache::get('expired_keys', []);
        
        foreach ($expiredKeys as $key) {
            Cache::forget($key);
        }
        
        Cache::put('expired_keys', [], now()->addHour());
    }
}
