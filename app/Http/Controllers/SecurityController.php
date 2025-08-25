<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence; // Added this import
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
use App\Services\GeolocationService; // Added this import
use Carbon\Carbon;

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
            $risk_distribution = $this->dashboardService->getRiskLevelDistribution();
            $threats_by_country = $this->dashboardService->getThreatsByCountry();
            $top_suspicious_ips = $this->dashboardService->getTopSuspiciousIPs();
            $recent_events = $this->dashboardService->getRecentEvents(10);
            $threat_trends = $this->dashboardService->getThreatTrends();
            $system_performance = $this->dashboardService->getSystemPerformance();

            // DEBUG TEMPORAL - ELIMINAR DESPUÉS
            Log::info('SecurityController: Datos obtenidos del servicio', [
                'risk_distribution' => $risk_distribution,
                'threats_by_country' => $threats_by_country,
                'top_suspicious_ips_count' => $top_suspicious_ips->count(),
                'recent_events_count' => $recent_events->count(),
            ]);

            $dashboardData = [
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
                'risk_distribution' => [0, 0, 0], // Solo 3 niveles: Crítico, Alto, Medio
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
            // Obtener eventos reales de la base de datos (día actual)
            $events = SecurityEvent::select(
                'id',
                'ip_address',
                'category',
                'threat_score',
                'risk_level',
                'created_at',
                'geolocation',
                'status',
                'reason'
            )
                ->whereDate('created_at', today()) // Solo día actual
                ->latest()
                ->take(150) // Limitar a 150 eventos para rendimiento
                ->get();

            // Mapear eventos para la vista
            $mappedEvents = $events->map(function ($event) {
                $geolocation = $event->geolocation ?? [];
                $country = $geolocation['country'] ?? 'N/A';

                return [
                    'id' => $event->id,
                    'ip_address' => $event->ip_address,
                    'category' => $event->category ?? 'unknown',
                    'threat_score' => $event->threat_score,
                    'risk_level' => $event->risk_level ?? 'medium',
                    'created_at' => $event->created_at,
                    'country' => $country,
                    'status' => $event->status ?? 'nuevo'
                ];
            });

            // Datos de estadísticas reales
            $data = [
                'events' => $mappedEvents,
                'criticalEventsCount' => SecurityEvent::where('threat_score', '>=', 80)->whereDate('created_at', today())->count(),
                'highEventsCount' => SecurityEvent::whereBetween('threat_score', [60, 79])->whereDate('created_at', today())->count(),
                'mediumEventsCount' => SecurityEvent::whereBetween('threat_score', [40, 59])->whereDate('created_at', today())->count(),
                'uniqueIPsCount' => SecurityEvent::whereDate('created_at', today())->distinct('ip_address')->count('ip_address'),
                'totalEventsCount' => SecurityEvent::whereDate('created_at', today())->count(),
            ];

            return view('security.events', $data);
        } catch (\Exception $e) {
            Log::error('Error en vista de eventos: ' . $e->getMessage());

            return view('security.events', [
                'events' => collect(),
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
        try {
            // Obtener datos reales de inteligencia de amenazas (últimos 3 días)
            $threats = ThreatIntelligence::select(
                'id',
                'ip_address',
                'threat_type',
                'threat_score',
                'classification',
                'confidence',
                'country_code',
                'status',
                'last_updated',
                'malware_family',
                'attack_vectors',
                'geographic_origin'
            )
                ->where('last_updated', '>=', now()->subDays(3)) // Solo últimos 3 días
                ->latest('last_updated')
                ->take(50) // Limitar a 50 amenazas para rendimiento
                ->get();

            // Mapear amenazas para la vista
            $mappedThreats = $threats->map(function ($threat) {
                return [
                    'id' => $threat->id,
                    'ip' => $threat->ip_address,
                    'type' => $threat->threat_type ?? 'unknown',
                    'classification' => $threat->classification ?? 'medium',
                    'score' => $threat->threat_score ?? 50,
                    'confidence' => $threat->confidence ?? 70,
                    'country' => $threat->country_code ?? 'N/A',
                    'status' => $threat->status ?? 'active',
                    'lastUpdated' => $threat->last_updated ?? now(),
                    'malwareFamily' => $threat->malware_family ?? 'N/A',
                    'attackVectors' => $threat->attack_vectors ?? [],
                    'geographicOrigin' => $threat->geographic_origin ?? 'N/A'
                ];
            });

            // Generar datos de evolución temporal (últimos 7 días)
            $evolutionData = $this->getThreatEvolutionData();

            // Obtener tipos de amenazas disponibles para filtros
            $threatTypes = $this->getAvailableThreatTypes();

            // Obtener países disponibles para filtros
            $countries = $this->getAvailableCountries();

            $data = [
                'threats' => $mappedThreats,
                'evolutionData' => $evolutionData,
                'threatTypes' => $threatTypes,
                'countries' => $countries,
                'totalThreats' => ThreatIntelligence::where('last_updated', '>=', now()->subDays(3))->count(),
                'activeThreats' => ThreatIntelligence::where('status', 'active')->where('last_updated', '>=', now()->subDays(3))->count(),
                'criticalThreats' => ThreatIntelligence::where('classification', 'critical')->where('last_updated', '>=', now()->subDays(3))->count(),
                'highThreats' => ThreatIntelligence::where('classification', 'high')->where('last_updated', '>=', now()->subDays(3))->count(),
            ];

            return view('security.threat-intelligence', $data);
        } catch (\Exception $e) {
            Log::error('Error en vista de inteligencia de amenazas: ' . $e->getMessage());

            return view('security.threat-intelligence', [
                'threats' => collect(),
                'evolutionData' => [],
                'totalThreats' => 0,
                'activeThreats' => 0,
                'criticalThreats' => 0,
                'highThreats' => 0,
            ]);
        }
    }

    /**
     * Vista de reputación de IPs
     */
    public function ipReputation()
    {
        try {
            // Obtener datos reales de reputación de IPs desde la base de datos (últimos 3 días)
            $ipReputations = DB::table('ip_reputations')->select(
                'ip_address',
                'reputation_score as threat_score',
                'risk_level',
                'geographic_data as geolocation',
                'network_data',
                'blacklisted',
                'created_at',
                DB::raw('CASE WHEN blacklisted = 1 THEN "blocked" ELSE "active" END as status'),
                DB::raw('"ip_reputation" as category')
            )
                ->where('created_at', '>=', now()->subDays(3)) // Solo últimos 3 días
                ->orderBy('created_at', 'desc') // Ordenar por fecha más reciente
                ->take(100) // Limitar a 100 IPs para rendimiento
                ->get();

            // Usar el servicio de geolocalización para obtener información real de países
            $geolocationService = app(GeolocationService::class);

            // Mapear datos para la vista con geolocalización real
            $mappedIPs = $ipReputations->map(function ($ip) use ($geolocationService) {
                // Obtener geolocalización real usando el servicio
                $geolocation = $geolocationService->getGeolocation($ip->ip_address);

                // Extraer datos de geolocalización de la base de datos si están disponibles
                // NOTA: Los campos son alias SQL, por eso usamos los nombres de los alias
                $dbGeolocation = json_decode($ip->geolocation, true) ?? [];
                $dbNetwork = json_decode($ip->network_data, true) ?? [];

                $country = $geolocation['country'] ?? $dbGeolocation['country'] ?? 'N/A';
                $city = $geolocation['city'] ?? $dbGeolocation['city'] ?? 'N/A';
                $isp = $geolocation['isp'] ?? $dbNetwork['isp'] ?? 'N/A';

                return [
                    'ip' => $ip->ip_address,
                    'score' => $ip->threat_score ?? 50,
                    'risk_level' => $ip->risk_level ?? 'medium',
                    'country' => $country,
                    'city' => $city,
                    'isp' => $isp,
                    'lastUpdated' => Carbon::parse($ip->created_at)->toISOString(),
                    'status' => $ip->status ?? 'active',
                    'category' => $ip->category ?? 'ip_reputation'
                ];
            });

            // Generar datos de distribución por riesgo (últimos 3 días)
            $riskDistribution = $this->getIPRiskDistribution($ipReputations);

            // Generar datos de distribución por país usando geolocalización real (últimos 3 días)
            $countryDistribution = $this->getIPCountryDistributionReal($mappedIPs);

            // Obtener países disponibles para filtros usando geolocalización real (últimos 3 días)
            $availableCountries = $this->getAvailableIPCountriesReal($mappedIPs);

            // DEBUG TEMPORAL - ELIMINAR DESPUÉS
            Log::info('IP Reputation Data Debug', [
                'total_mapped_ips' => $mappedIPs->count(),
                'first_ip_sample' => $mappedIPs->first(),
                'risk_distribution' => $riskDistribution,
                'country_distribution' => $countryDistribution,
                'raw_data_sample' => $ipReputations->first(),
                'mapped_data_sample' => $mappedIPs->first(),
                'raw_data_structure' => $ipReputations->first() ? get_object_vars($ipReputations->first()) : null,
                'date_format_sample' => $mappedIPs->first() ? $mappedIPs->first()['lastUpdated'] : null
            ]);

            // Contadores actualizados para últimos 3 días
            $data = [
                'ipReputations' => $mappedIPs,
                'riskDistribution' => $riskDistribution,
                'countryDistribution' => $countryDistribution,
                'availableCountries' => $availableCountries,
                'totalIPs' => DB::table('ip_reputations')->where('created_at', '>=', now()->subDays(3))->count(),
                'criticalIPs' => DB::table('ip_reputations')->where('risk_level', 'critical')->where('created_at', '>=', now()->subDays(3))->count(),
                'highIPs' => DB::table('ip_reputations')->where('risk_level', 'high')->where('created_at', '>=', now()->subDays(3))->count(),
                'mediumIPs' => DB::table('ip_reputations')->where('risk_level', 'medium')->where('created_at', '>=', now()->subDays(3))->count(),
            ];

            return view('security.ip-reputation', $data);

        } catch (\Exception $e) {
            Log::error('Error en vista de reputación de IPs: ' . $e->getMessage());

            return view('security.ip-reputation', [
                'ipReputations' => collect(),
                'riskDistribution' => [],
                'countryDistribution' => [],
                'availableCountries' => [],
                'totalIPs' => 0,
                'criticalIPs' => 0,
                'highIPs' => 0,
                'mediumIPs' => 0,
            ]);
        }
    }

    /**
     * Obtener estadísticas de reputación de IPs desde la BD
     */
    private function getIPReputationStats()
    {
        try {
            // Solo obtener datos básicos sin métricas
            $totalIPs = SecurityEvent::distinct('ip_address')->count('ip_address');

            return [
                'total_ips' => $totalIPs,
                'clean_ips' => 0,
                'suspicious_ips' => 0,
                'malicious_ips' => 0,
                'avg_score' => 0,
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
    public function logs(Request $request)
    {
        try {
            // Parámetros de paginación
            $perPage = $request->get('per_page', 25);
            $page = $request->get('page', 1);

            // Filtros
            $level = $request->get('level');
            $source = $request->get('source');
            $search = $request->get('search');
            $ip = $request->get('ip');
            $date = $request->get('date');

            // Obtener logs del archivo de Laravel
            $logFile = storage_path('logs/laravel.log');
            $logs = collect();

            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $logLines = explode("\n", $logContent);

                foreach ($logLines as $line) {
                    if (empty(trim($line)))
                        continue;

                    // Parsear línea de log de Laravel
                    $logEntry = $this->parseLogLine($line);
                    if ($logEntry) {
                        // Aplicar filtros
                        if ($level && $logEntry['level'] !== $level)
                            continue;
                        if ($source && $logEntry['source'] !== $source)
                            continue;
                        if ($search && !str_contains(strtolower($logEntry['message']), strtolower($search)))
                            continue;
                        if ($ip && !str_contains($logEntry['ip'], $ip))
                            continue;
                        if ($date && !str_starts_with($logEntry['timestamp'], $date))
                            continue;

                        $logs->push($logEntry);
                    }
                }
            }

            // Si no hay logs reales, mostrar mensaje de no hay logs
            if ($logs->isEmpty()) {
                $logs = collect();
            }

            // Ordenar por timestamp más reciente
            $logs = $logs->sortByDesc('timestamp');

            // Paginar
            $totalLogs = $logs->count();
            $logs = $logs->forPage($page, $perPage);

            // Generar datos de paginación
            $pagination = [
                'current_page' => (int) $page,
                'per_page' => (int) $perPage,
                'total' => $totalLogs,
                'last_page' => ceil($totalLogs / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $totalLogs),
            ];

            return view('security.logs', compact('logs', 'pagination'));

        } catch (\Exception $e) {
            Log::error('Error en vista de logs: ' . $e->getMessage());

            return view('security.logs', [
                'logs' => collect(),
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 25,
                    'total' => 0,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0,
                ]
            ]);
        }
    }

    /**
     * Parsear línea de log de Laravel
     */
    private function parseLogLine(string $line): ?array
    {
        // Patrón para logs de Laravel: [2024-01-15 10:30:45] local.INFO: Mensaje del log
        if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'source' => $matches[2],
                'level' => strtolower($matches[3]),
                'message' => $matches[4],
                'ip' => $this->extractIPFromMessage($matches[4]),
                'user_id' => $this->extractUserIdFromMessage($matches[4]),
            ];
        }

        return null;
    }

    /**
     * Extraer IP del mensaje del log
     */
    private function extractIPFromMessage(string $message): ?string
    {
        if (preg_match('/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/', $message, $matches)) {
            return $matches[0];
        }
        return null;
    }

    /**
     * Extraer ID de usuario del mensaje del log
     */
    private function extractUserIdFromMessage(string $message): ?string
    {
        if (preg_match('/user[_-]?(\d+)/i', $message, $matches)) {
            return $matches[1];
        }
        return null;
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
                Log::error('⚠️ No hay eventos en la base de datos');
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
     * Obtener datos de evolución de amenazas (últimos 3 días)
     */
    private function getThreatEvolutionData(): array
    {
        try {
            $dates = [];
            $criticalData = [];
            $highData = [];

            for ($i = 2; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dates[] = $date->format('d/m');

                // Contar amenazas críticas del día
                $criticalCount = ThreatIntelligence::where('classification', 'critical')
                    ->whereDate('last_updated', $date)
                    ->count();

                // Contar amenazas altas del día
                $highCount = ThreatIntelligence::where('classification', 'high')
                    ->whereDate('last_updated', $date)
                    ->count();

                $criticalData[] = $criticalCount;
                $highData[] = $highCount;
            }

            return [
                'dates' => $dates,
                'critical' => $criticalData,
                'high' => $highData
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos de evolución: ' . $e->getMessage());
            return [
                'dates' => [],
                'critical' => [],
                'high' => []
            ];
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

        // Solo retornar los 3 niveles principales
        return 'medium';
    }

    /**
     * Obtener tipos de amenazas disponibles para filtros
     */
    private function getAvailableThreatTypes(): array
    {
        try {
            $types = ThreatIntelligence::select('threat_type')
                ->distinct()
                ->whereNotNull('threat_type')
                ->pluck('threat_type')
                ->toArray();

            $typeNames = [
                'malware' => 'Malware',
                'phishing' => 'Phishing',
                'ddos' => 'DDoS',
                'apt' => 'APT',
                'ransomware' => 'Ransomware',
                'botnet' => 'Botnet',
                'sql_injection' => 'SQL Injection',
                'xss' => 'XSS Attack'
            ];

            $availableTypes = [];
            foreach ($types as $type) {
                if (isset($typeNames[$type])) {
                    $availableTypes[$type] = $typeNames[$type];
                }
            }

            return $availableTypes;
        } catch (\Exception $e) {
            Log::error('Error obteniendo tipos de amenazas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener países disponibles para filtros
     */
    private function getAvailableCountries(): array
    {
        try {
            $countries = ThreatIntelligence::select('country_code', 'geographic_origin')
                ->distinct()
                ->whereNotNull('country_code')
                ->where('country_code', '!=', '')
                ->get();

            $availableCountries = [];
            foreach ($countries as $country) {
                $code = $country->country_code;
                $name = $country->geographic_origin ?: $code;
                $availableCountries[$code] = $name;
            }

            return $availableCountries;
        } catch (\Exception $e) {
            Log::error('Error obteniendo países: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generar datos de distribución por riesgo de IPs
     */
    private function getIPRiskDistribution($ipReputations): array
    {
        $riskDistribution = [];
        $totalIPs = $ipReputations->count();

        if ($totalIPs === 0) {
            return [
                'critical' => 0,
                'high' => 0,
                'medium' => 0,
                'total' => 0,
            ];
        }

        $riskDistribution['critical'] = $ipReputations->filter(function ($ip) {
            return $ip->risk_level === 'critical';
        })->count();
        $riskDistribution['high'] = $ipReputations->filter(function ($ip) {
            return $ip->risk_level === 'high';
        })->count();
        $riskDistribution['medium'] = $ipReputations->filter(function ($ip) {
            return $ip->risk_level === 'medium';
        })->count();
        $riskDistribution['total'] = $totalIPs;

        return $riskDistribution;
    }

    /**
     * Generar datos de distribución por país de IPs
     */
    private function getIPCountryDistribution($ipReputations): array
    {
        $countryDistribution = [];
        $totalIPs = $ipReputations->count();

        if ($totalIPs === 0) {
            return [
                'N/A' => 0,
                'total' => 0,
            ];
        }

        $countryDistribution = $ipReputations->groupBy('country')
            ->map(function ($ips, $country) use ($totalIPs) {
                return [
                    'country' => $country,
                    'count' => $ips->count(),
                    'percentage' => round(($ips->count() / $totalIPs) * 100, 2),
                ];
            })
            ->toArray();

        return $countryDistribution;
    }

    /**
     * Generar datos de distribución por país de IPs usando geolocalización real
     */
    private function getIPCountryDistributionReal($mappedIPs): array
    {
        $countryDistribution = [];
        $totalIPs = $mappedIPs->count();

        if ($totalIPs === 0) {
            return [
                'N/A' => [
                    'country' => 'N/A',
                    'count' => 0,
                    'percentage' => 0
                ],
                'total' => 0
            ];
        }

        // Agrupar IPs por país
        $countryCounts = $mappedIPs->groupBy('country')
            ->map(function ($ips, $country) use ($totalIPs) {
                return [
                    'country' => $country,
                    'count' => $ips->count(),
                    'percentage' => round(($ips->count() / $totalIPs) * 100, 2),
                ];
            })
            ->toArray();

        // Agregar total
        $countryCounts['total'] = $totalIPs;

        return $countryCounts;
    }

    /**
     * Obtener países disponibles para filtros de IPs usando geolocalización real
     */
    private function getAvailableIPCountriesReal($mappedIPs): array
    {
        return $mappedIPs->pluck('country')
            ->unique()
            ->filter(function ($country) {
                return $country !== 'N/A' && !empty($country);
            })
            ->values()
            ->toArray();
    }



}
