<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use App\Services\SimpleSecurityService;
use App\Services\SecurityDashboardService;
use App\Http\Resources\SecurityEventResource;
use App\Http\Resources\SecurityDashboardResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SecurityController extends Controller
{
    protected $simpleSecurity;
    protected $dashboardService;

    public function __construct(SimpleSecurityService $simpleSecurity, SecurityDashboardService $dashboardService)
    {
        $this->simpleSecurity = $simpleSecurity;
        $this->dashboardService = $dashboardService;
    }
    /**
     * Dashboard principal de seguridad (REFACTORIZADO)
     */
    public function index()
    {
        try {
            // DEBUG TEMPORAL - ELIMINAR DESPUÉS
            Log::info('SecurityController: Iniciando obtención de datos del dashboard');
            
            // Usar el servicio del dashboard para obtener todos los datos
            $metrics = $this->dashboardService->getMainMetrics();
            $risk_distribution = $this->dashboardService->getRiskLevelDistribution();
            $threats_by_country = $this->dashboardService->getThreatsByCountry();
            $top_suspicious_ips = $this->dashboardService->getTopSuspiciousIPs();
            $recent_events = $this->dashboardService->getRecentEvents(10);
            $threat_trends = $this->dashboardService->getThreatTrends();
            $system_performance = $this->dashboardService->getSystemPerformance();
            
            // DEBUG TEMPORAL - ELIMINAR DESPUÉS
            Log::info('SecurityController: Datos obtenidos del servicio', [
                'metrics' => $metrics,
                'risk_distribution' => $risk_distribution,
                'threats_by_country' => $threats_by_country,
                'top_suspicious_ips_count' => $top_suspicious_ips->count(),
                'recent_events_count' => $recent_events->count(),
            ]);
            
            $dashboardData = [
                'metrics' => $metrics,
                'risk_distribution' => $risk_distribution,
                'threats_by_country' => $threats_by_country,
                'top_suspicious_ips' => $top_suspicious_ips,
                'recent_events' => $recent_events,
                'threat_trends' => $threat_trends,
                'system_performance' => $system_performance,
            ];

            return view('security.index', $dashboardData);
        } catch (\Exception $e) {
            Log::error('Error en dashboard de seguridad: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // En caso de error, mostrar vista con datos vacíos
            return view('security.index', [
                'metrics' => [],
                'risk_distribution' => [0, 0, 0, 0, 0],
                'threats_by_country' => [],
                'top_suspicious_ips' => collect(),
                'recent_events' => collect(),
                'threat_trends' => [],
                'system_performance' => [],
            ]);
        }
    }









    /**
     * Vista de eventos de seguridad
     */
    public function events()
    {
        try {
            // Datos reales de la base de datos para estadísticas
            $data = [
                'criticalEventsCount' => SecurityEvent::where('threat_score', '>=', 80)->where('created_at', '>=', now()->subDays(7))->count(),
                'highEventsCount' => SecurityEvent::whereBetween('threat_score', [60, 79])->where('created_at', '>=', now()->subDays(7))->count(),
                'mediumEventsCount' => SecurityEvent::whereBetween('threat_score', [40, 59])->where('created_at', '>=', now()->subDays(7))->count(),
                'uniqueIPsCount' => SecurityEvent::where('created_at', '>=', now()->subDays(7))->distinct('ip_address')->count('ip_address'),
                'totalEventsCount' => SecurityEvent::where('created_at', '>=', now()->subDays(7))->count(),
            ];

            return view('security.events', $data);
        } catch (\Exception $e) {
            Log::error('Error en vista de eventos: ' . $e->getMessage());
            
            return view('security.events', [
                'criticalEventsCount' => 0,
                'highEventsCount' => 0,
                'mediumEventsCount' => 0,
                'uniqueIPsCount' => 0,
                'totalEventsCount' => 0,
            ]);
        }
    }

    /**
     * Vista de inteligencia de amenazas
     */
    public function threatIntelligence()
    {
        return view('security.threat-intelligence');
    }

    /**
     * Vista de reputación de IPs
     */
    public function ipReputation()
    {
        // Obtener datos reales de la base de datos
        $ipStats = $this->getIPReputationStats();

        return view('security.ip-reputation', compact('ipStats'));
    }

    /**
     * Obtener estadísticas de reputación de IPs desde la BD
     */
    private function getIPReputationStats()
    {
        try {
            // Intentar obtener datos de la tabla IPReputation si existe
            if (Schema::hasTable('ip_reputations')) {
                $totalIPs = \App\Models\IPReputation::count();
                $cleanIPs = \App\Models\IPReputation::where('risk_score', '<', 30)->count();
                $suspiciousIPs = \App\Models\IPReputation::whereBetween('risk_score', [30, 70])->count();
                $maliciousIPs = \App\Models\IPReputation::where('risk_score', '>', 70)->count();
                $avgScore = \App\Models\IPReputation::avg('risk_score') ?? 0;
            } else {
                // Si la tabla no existe, usar datos del cache o valores por defecto
                $totalIPs = Cache::get('security.total_ips', 0);
                $cleanIPs = Cache::get('security.clean_ips', 0);
                $suspiciousIPs = Cache::get('security.suspicious_ips', 0);
                $maliciousIPs = Cache::get('security.malicious_ips', 0);
                $avgScore = Cache::get('security.avg_score', 0);
            }

            return [
                'total_ips' => $totalIPs,
                'clean_ips' => $cleanIPs,
                'suspicious_ips' => $suspiciousIPs,
                'malicious_ips' => $maliciousIPs,
                'avg_score' => round($avgScore, 1),
            ];

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de IPs: ' . $e->getMessage());

            // Valores por defecto en caso de error
            return [
                'total_ips' => 0,
                'clean_ips' => 0,
                'suspicious_ips' => 0,
                'malicious_ips' => 0,
                'avg_score' => 0,
            ];
        }
    }





    /**
     * Vista de logs de seguridad
     */
    public function logs()
    {
        return view('security.logs');
    }



    /**
     * Agregar IP a whitelist
     */
    public function whitelistIP(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ip' => 'required|ip',
                'reason' => 'nullable|string|max:500',
                'permanent' => 'boolean',
            ]);

            $ip = $request->ip;
            $reason = $request->reason ?? 'Whitelist manual por administrador';
            $permanent = $request->permanent ?? false;

            // Agregar IP a whitelist
            $whitelist = Cache::get('security.whitelist', []);
            $whitelist[$ip] = [
                'reason' => $reason,
                'added_at' => now(),
                'permanent' => $permanent,
                'added_by' => auth()->id(),
            ];
            Cache::put('security.whitelist', $whitelist, now()->addDays(365));

            // Remover de blacklist si existe
            $blacklist = Cache::get('security.blacklist', []);
            if (isset($blacklist[$ip])) {
                unset($blacklist[$ip]);
                Cache::put('security.blacklist', $blacklist, now()->addDays(30));
            }

            // Registrar evento de seguridad
            SecurityEvent::create([
                'ip_address' => $ip,
                'event_type' => 'manual_whitelist',
                'threat_score' => 0,
                'details' => [
                    'reason' => $reason,
                    'permanent' => $permanent,
                    'added_by' => auth()->id(),
                ],
                'created_at' => now(),
            ]);

            Log::info("IP {$ip} agregada a whitelist por usuario " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "IP {$ip} agregada a whitelist exitosamente",
                'data' => [
                    'ip' => $ip,
                    'permanent' => $permanent,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("Error al agregar IP a whitelist: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar IP a whitelist: ' . $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Estadísticas del dashboard (REFACTORIZADO)
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $dashboardData = [
                'metrics' => $this->dashboardService->getMainMetrics(),
                'risk_distribution' => $this->dashboardService->getRiskLevelDistribution(),
                'threats_by_country' => $this->dashboardService->getThreatsByCountry(),
                'top_suspicious_ips' => $this->dashboardService->getTopSuspiciousIPs(),
                'recent_events' => $this->dashboardService->getRecentEvents(10),
                'threat_trends' => $this->dashboardService->getThreatTrends(),
                'system_performance' => $this->dashboardService->getSystemPerformance(),
            ];

            return (new SecurityDashboardResource($dashboardData))->response();
        } catch (\Exception $e) {
            Log::error("Error al obtener estadísticas del dashboard: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }

    /**
     * Obtener eventos de seguridad paginados
     */
    public function getSecurityEvents(Request $request): JsonResponse
    {
        try {
            $query = SecurityEvent::query();

            // Filtros
            if ($request->filled('ip')) {
                $query->where('ip_address', 'like', '%' . $request->ip . '%');
            }

            if ($request->filled('event_type')) {
                $query->where('event_type', $request->event_type);
            }



            if ($request->filled('min_threat_score')) {
                $query->where('threat_score', '>=', $request->min_threat_score);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $events = $query->latest()->paginate($request->get('per_page', 25));

            return response()->json([
                'success' => true,
                'data' => $events,
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener eventos de seguridad: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener eventos',
            ], 500);
        }
    }

    /**
     * Calcular tasa de prevención
     */
    private function calculatePreventionRate(): float
    {
        $totalEvents = SecurityEvent::count();
        if ($totalEvents === 0) {
            return 100.0;
        }

        $preventedEvents = SecurityEvent::where('threat_score', '>=', 70)->count();
        return round(($preventedEvents / $totalEvents) * 100, 1);
    }

    /**
     * Obtener IPs más sospechosas
     */
    private function getTopSuspiciousIPs(): array
    {
        return SecurityEvent::select('ip_address')
            ->selectRaw('AVG(threat_score) as avg_threat_score')
            ->selectRaw('COUNT(*) as event_count')
            ->groupBy('ip_address')
            ->orderByDesc('avg_threat_score')
            ->take(10)
            ->get()
            ->toArray();
    }

    /**
     * Obtener distribución de amenazas
     */
    private function getThreatDistribution(): array
    {
        return SecurityEvent::select('event_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('event_type')
            ->get()
            ->toArray();
    }

    /**
     * Obtener estado del sistema
     */
    private function getSystemStatus(): array
    {
        return [
            'security_enabled' => config('security.enabled', true),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'last_scan' => Cache::get('security.last_scan', 'Nunca'),
            'active_threats' => SecurityEvent::where('threat_score', '>=', 70)
                ->where('created_at', '>=', now()->subHours(24))
                ->count(),
        ];
    }

    /**
     * Obtener datos de eventos para la vista de lista
     */
    public function getEventsData(Request $request)
    {
        try {
            Log::info('🔍 Iniciando getEventsData()');
            Log::info('📅 Filtros recibidos: ' . json_encode($request->all()));
            
            // Verificar si la tabla existe y tiene datos
            $totalEvents = SecurityEvent::count();
            Log::info("📊 Total de eventos en BD: {$totalEvents}");
            
            if ($totalEvents === 0) {
                Log::warning('⚠️ No hay eventos en la base de datos');
                return response()->json([
                    'success' => true,
                    'events' => [],
                    'message' => 'No hay eventos de seguridad',
                    'total_in_db' => 0
                ]);
            }
            
            $query = SecurityEvent::select(
                'id',
                'ip_address',
                'category',
                'threat_score',
                'created_at',
                'geolocation',
                'reason'
            );

            // Aplicar filtro de fecha si se proporciona
            if ($request->has('date')) {
                $dateFilter = $request->input('date');
                Log::info("📅 Aplicando filtro de fecha: {$dateFilter}");
                
                switch ($dateFilter) {
                    case '24h':
                        $query->where('created_at', '>=', now()->subHours(24));
                        Log::info('⏰ Filtro: Últimas 24 horas aplicado');
                        break;
                    case '7d':
                        $query->where('created_at', '>=', now()->subDays(7));
                        Log::info('⏰ Filtro: Últimos 7 días aplicado');
                        break;
                    default:
                        Log::info('⏰ Sin filtro de fecha específico');
                        break;
                }
            }
            
            $events = $query->latest()->take(50)->get();
                
            Log::info("📋 Eventos consultados: {$events->count()}");
            
            $mappedEvents = $events->map(function ($event) {
                // Extraer información geográfica real
                $geolocation = $event->geolocation ?? [];
                $country = $geolocation['country'] ?? 'N/A';
                $city = $geolocation['city'] ?? 'N/A';
                
                return [
                    'id' => $event->id,
                    'ip' => $event->ip_address,
                    'score' => $event->threat_score,
                    'risk_level' => $this->getRiskLevelFromScore($event->threat_score),
                    'category' => $event->category ?? 'unknown',
                    'action' => 'monitor',
                    'date' => $event->created_at->format('d/m/Y, H:i'),
                    'status' => 'open',
                    'country' => $country,
                    'city' => $city,
                    'reason' => $event->reason ?? 'Evento de seguridad detectado',
                ];
            });

            Log::info('✅ Eventos mapeados exitosamente');
            
            return response()->json([
                'success' => true,
                'events' => SecurityEventResource::collection($events),
                'total_in_db' => $totalEvents,
                'total_returned' => $events->count()
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error obteniendo datos de eventos: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'events' => [],
                'message' => 'Error al cargar eventos: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener nivel de riesgo desde score
     */
    private function getRiskLevelFromScore($score)
    {
        if ($score >= 80) {
            return 'critical';
        }

        if ($score >= 60) {
            return 'high';
        }

        if ($score >= 40) {
            return 'medium';
        }

        if ($score >= 20) {
            return 'low';
        }

        return 'minimal';
    }


}
