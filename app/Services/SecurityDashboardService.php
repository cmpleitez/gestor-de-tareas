<?php

namespace App\Services;

use App\Models\SecurityEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SecurityDashboardService
{
    /**
     * Obtener distribución de eventos por nivel de riesgo
     */
    public function getRiskLevelDistribution(): array
    {
        return Cache::remember('security.risk_distribution', 600, function () {
            $last30d = Carbon::now()->subDays(30);

            // Solo 3 niveles: Crítico, Alto y Medio
            $distribution = [];

            // Contar eventos por rango de threat_score
            $distribution['critical'] = SecurityEvent::where('threat_score', '>=', 80)
                ->where('created_at', '>=', $last30d)
                ->count();

            $distribution['high'] = SecurityEvent::whereBetween('threat_score', [60, 79])
                ->where('created_at', '>=', $last30d)
                ->count();

            $distribution['medium'] = SecurityEvent::whereBetween('threat_score', [40, 59])
                ->where('created_at', '>=', $last30d)
                ->count();

            // Asegurar que todos los niveles estén presentes
            $levels = ['critical', 'high', 'medium'];
            foreach ($levels as $level) {
                if (!isset($distribution[$level])) {
                    $distribution[$level] = 0;
                }
            }

            return array_values($distribution);
        });
    }

    /**
     * Obtener amenazas por país
     */
    public function getThreatsByCountry(): array
    {
        return Cache::remember('security.threats_by_country', 900, function () {
            $last30d = Carbon::now()->subDays(30);

            return SecurityEvent::selectRaw('
                    JSON_UNQUOTE(JSON_EXTRACT(geolocation, "$.country")) as country,
                    COUNT(*) as count,
                    AVG(threat_score) as avg_score
                ')
                ->whereNotNull('geolocation')
                ->where('threat_score', '>=', 40) // Solo nivel Medio, Alto y Crítico
                ->where('created_at', '>=', $last30d)
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->country => [
                            'count' => $item->count,
                            'avg_score' => round($item->avg_score, 1)
                        ]
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Obtener IPs más sospechosas
     */
    public function getTopSuspiciousIPs(): Collection
    {
        return Cache::remember('security.top_suspicious_ips', 600, function () {
            $last7d = Carbon::now()->subDays(7);

            return SecurityEvent::select('ip_address')
                ->selectRaw('AVG(threat_score) as avg_threat_score')
                ->selectRaw('COUNT(*) as event_count')
                ->selectRaw('MAX(threat_score) as max_threat_score')
                ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(geolocation, "$.country")) as country')
                ->where('threat_score', '>=', 60)
                ->where('created_at', '>=', $last7d)
                ->groupBy('ip_address', 'country')
                ->orderByDesc('avg_threat_score')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Obtener eventos recientes
     */
    public function getRecentEvents(int $limit = 10): Collection
    {
        return SecurityEvent::with(['user'])
            ->select('id', 'ip_address', 'threat_score', 'risk_level', 'category', 'created_at', 'geolocation')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($event) {
                $event->formatted_created_at = $event->created_at->diffForHumans();
                $event->country = $event->geolocation['country'] ?? 'Unknown';
                $event->city = $event->geolocation['city'] ?? 'Unknown';
                return $event;
            });
    }

    /**
     * Obtener tendencias de amenazas
     */
    public function getThreatTrends(): array
    {
        return Cache::remember('security.threat_trends', 1800, function () {
            $now = Carbon::now();
            $trends = [];

            // Últimos 7 días
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();

                $trends[] = [
                    'date' => $date->format('M d'),
                    'events' => SecurityEvent::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
                    'avg_score' => SecurityEvent::whereBetween('created_at', [$startOfDay, $endOfDay])->avg('threat_score') ?? 0,
                    'critical' => SecurityEvent::critical()->whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
                ];
            }

            return $trends;
        });
    }

    /**
     * Obtener estadísticas de rendimiento del sistema
     */
    public function getSystemPerformance(): array
    {
        return Cache::remember('security.system_performance', 3600, function () {
            $last24h = Carbon::now()->subDay();

            return [
                'response_time_avg' => $this->calculateAverageResponseTime($last24h),
                'threat_detection_rate' => $this->calculateThreatDetectionRate($last24h),
                'false_positive_rate' => $this->calculateFalsePositiveRate($last24h),
                'system_uptime' => $this->calculateSystemUptime(),
            ];
        });
    }

    /**
     * Calcular tiempo de respuesta promedio
     */
    private function calculateAverageResponseTime(Carbon $since): float
    {
        // Simulado por ahora - en producción se obtendría de logs reales
        return rand(50, 200) / 1000; // 50-200ms en segundos
    }

    /**
     * Calcular tasa de detección de amenazas
     */
    private function calculateThreatDetectionRate(Carbon $since): float
    {
        $totalEvents = SecurityEvent::where('created_at', '>=', $since)->count();
        $detectedThreats = SecurityEvent::where('created_at', '>=', $since)
            ->where('threat_score', '>=', 40)
            ->count();

        return $totalEvents > 0 ? round(($detectedThreats / $totalEvents) * 100, 1) : 0;
    }

    /**
     * Calcular tasa de falsos positivos
     */
    private function calculateFalsePositiveRate(Carbon $since): float
    {
        // Simulado por ahora - en producción se obtendría de análisis manual
        return rand(5, 15) / 100; // 5-15%
    }

    /**
     * Calcular uptime del sistema
     */
    private function calculateSystemUptime(): float
    {
        // Simulado por ahora - en producción se obtendría de monitoreo real
        return 99.8; // 99.8%
    }

    /**
     * Limpiar cache del dashboard
     */
    public function clearCache(): void
    {
        Cache::forget('security.risk_distribution');
        Cache::forget('security.threats_by_country');
        Cache::forget('security.top_suspicious_ips');
        Cache::forget('security.threat_trends');
        Cache::forget('security.system_performance');
    }
}
