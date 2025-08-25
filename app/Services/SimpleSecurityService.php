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

    /**
     * Obtener reputaciones de IPs desde eventos de seguridad (últimos 3 días)
     */
    public function getIPReputations()
    {
        return SecurityEvent::select('ip_address as ip', 'threat_score as score', 'created_at as lastUpdated', 'geolocation')
            ->whereNotNull('ip_address')
            ->where('ip_address', '!=', '')
            ->where('created_at', '>=', now()->subDays(3)) // ← Filtrar últimos 3 días
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($event) {
                $geolocation = $event->geolocation ?? [];
                
                return [
                    'ip' => $event->ip,
                    'score' => $event->score,
                    'risk_level' => $this->categorizeRisk($event->score),
                    'country' => $geolocation['country'] ?? 
                                $geolocation['country_name'] ?? 
                                $geolocation['countryCode'] ?? 
                                'Desconocido',
                    'isp' => $geolocation['isp'] ?? 
                            $geolocation['organization'] ?? 
                            'Desconocido',
                    'lastUpdated' => $event->lastUpdated,
                    'status' => $event->score >= 80 ? 'Bloqueada' : 'Monitoreando'
                ];
            });
    }

    /**
     * Obtener distribución de riesgo
     */
    public function getRiskDistribution(): array
    {
        $events = SecurityEvent::whereNotNull('ip_address')
            ->where('ip_address', '!=', '')
            ->get();

        $distribution = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'minimal' => 0
        ];

        foreach ($events as $event) {
            $riskLevel = $this->categorizeRisk($event->threat_score);
            $distribution[$riskLevel]++;
        }

        return $distribution;
    }

    /**
     * Obtener distribución por país usando datos de geolocalización (últimos 3 días)
     */
    public function getCountryDistribution(): array
    {
        $events = SecurityEvent::whereNotNull('ip_address')
            ->where('ip_address', '!=', '')
            ->whereNotNull('geolocation')
            ->where('geolocation', '!=', '[]')
            ->where('created_at', '>=', now()->subDays(3)) // ← Filtrar últimos 3 días
            ->get();

        $countryCounts = [];
        
        foreach ($events as $event) {
            $geolocation = $event->geolocation;
            
            // Extraer país del array de geolocalización
            $country = $geolocation['country'] ?? 
                      $geolocation['country_name'] ?? 
                      $geolocation['countryCode'] ?? 
                      'Desconocido';
            
            if (!isset($countryCounts[$country])) {
                $countryCounts[$country] = 0;
            }
            $countryCounts[$country]++;
        }

        // Si no hay datos de geolocalización, usar IPs únicas
        if (empty($countryCounts)) {
            $uniqueIPs = SecurityEvent::whereNotNull('ip_address')
                ->where('ip_address', '!=', '')
                ->distinct('ip_address')
                ->count();
            
            return [
                'Sin datos de ubicación' => ['count' => $uniqueIPs]
            ];
        }

        // Convertir a formato esperado por la vista
        $result = [];
        foreach ($countryCounts as $country => $count) {
            $result[$country] = ['count' => $count];
        }

        return $result;
    }
}
