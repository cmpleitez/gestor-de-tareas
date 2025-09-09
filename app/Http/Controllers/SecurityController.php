<?php
namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence;
use App\Services\SecurityDashboardService;
use App\Services\SimpleSecurityService;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    protected $simpleSecurity;
    protected $dashboardService;

    public function __construct(SimpleSecurityService $simpleSecurity, SecurityDashboardService $dashboardService)
    {
        $this->simpleSecurity   = $simpleSecurity;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Vista de logs de seguridad - VERSIÓN SIMPLIFICADA
     */
    public function logs(Request $request)
    {
        try {
            // Parámetros de paginación
            $perPage = $request->get('per_page', 25);
            $page    = $request->get('page', 1);

            // Filtros
            $source = $request->get('source');
            $search = $request->get('search');
            $ip     = $request->get('ip');
            $date   = $request->get('date');

            // Leer logs reales del sistema de seguridad (EXCLUYENDO laravel.log para evitar loop infinito)
            $logs        = collect();
            $filesToRead = [];

            // Definir archivos de logs de seguridad a leer
            $logFiles = [
                'security' => storage_path('logs/security.log'),
                'firewall' => storage_path('logs/firewall.log'),
                'ids'      => storage_path('logs/ids.log'),
            ];

            // Leer cada archivo de log si existe
            foreach ($logFiles as $logSource => $filePath) {
                if (file_exists($filePath) && is_readable($filePath)) {
                    $fileToRead    = $logSource;
                    $filesToRead[] = $fileToRead;

                    try {
                        $fileContent = file_get_contents($filePath);
                        if ($fileContent) {
                            $lines = explode("\n", $fileContent);

                            foreach ($lines as $line) {
                                $line = trim($line);
                                if (empty($line)) {
                                    continue;
                                }

                                $parsedLog = $this->parseLogLine($line);
                                if ($parsedLog) {
                                    $parsedLog['source'] = $logSource;
                                    $logs->push($parsedLog);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Error silencioso para evitar loop infinito
                    }
                }
            }

            // Asegurar que $logs sea siempre una colección válida
            if ($logs->isEmpty()) {
                $logs = collect();
            }

            // APLICAR FILTROS (incluyendo fuente)
            if ($source && $source !== 'all') {
                $logs = $logs->filter(function ($logEntry) use ($source) {
                    return $logEntry['source'] === $source;
                });
            }

            if ($search || $ip || $date) {
                $logs = $logs->filter(function ($logEntry) use ($search, $ip, $date) {
                    // Filtro por búsqueda de texto
                    if ($search && ! str_contains(strtolower($logEntry['message']), strtolower($search))) {
                        return false;
                    }

                    // Filtro por IP
                    if ($ip && ! str_contains($logEntry['ip'], $ip)) {
                        return false;
                    }

                    // Filtro por fecha
                    if ($date && ! str_starts_with($logEntry['timestamp'], $date)) {
                        return false;
                    }

                    return true;
                });
            }

            // Ordenar por timestamp más reciente (solo si hay logs)
            if ($logs->isNotEmpty()) {
                $logs = $logs->sortByDesc('timestamp');
            }

            // Paginar
            $totalLogs = $logs->count();
            if ($totalLogs > 0) {
                $logs = $logs->forPage($page, $perPage);
            }

            // Generar datos de paginación
            $pagination = [
                'current_page' => (int) $page,
                'per_page'     => (int) $perPage,
                'total'        => $totalLogs,
                'last_page'    => ceil($totalLogs / $perPage),
                'from'         => ($page - 1) * $perPage + 1,
                'to'           => min($page * $perPage, $totalLogs),
            ];

            // Asegurar que logs sea siempre un array válido
            $logsArray = $logs->isNotEmpty() ? $logs->values()->toArray() : [];

            // Filtrar elementos vacíos o nulos
            $logsArray = array_filter($logsArray, function ($log) {
                return ! empty($log) && is_array($log) && ! empty($log['message']);
            });

            // Reindexar el array después del filtrado
            $logsArray = array_values($logsArray);

            // Validación final antes de enviar a la vista
            if (! is_array($logsArray)) {
                $logsArray = [];
            }

            return view('security.logs', [
                'logs'       => $logsArray,
                'pagination' => $pagination,
            ]);

        } catch (\Exception $e) {
            // Error silencioso para evitar loop infinito
            return view('security.logs', [
                'logs'       => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page'     => 25,
                    'total'        => 0,
                    'last_page'    => 1,
                    'from'         => 0,
                    'to'           => 0,
                ],
            ]);
        }
    }

    /**
     * Parsear línea de log de múltiples formatos
     */
    private function parseLogLine(string $line): ?array
    {

        // Patrón 1: [timestamp] source.level: mensaje (formato Laravel estándar)
        if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {

            return [
                'timestamp' => $matches[1],
                'source'    => $matches[2],
                'message'   => $matches[4],
                'ip'        => $this->extractIPFromMessage($matches[4]),
                'user_id'   => $this->extractUserIdFromMessage($matches[4]),
            ];
        }

        // Patrón 2: [timestamp] mensaje (formato genérico)
        if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (.+)$/', $line, $matches)) {
            $message = $matches[2];

            // Intentar extraer source.level del mensaje
            if (preg_match('/^(\w+)\.(\w+): (.+)$/', $message, $subMatches)) {

                return [
                    'timestamp' => $matches[1],
                    'source'    => $subMatches[1],
                    'message'   => $subMatches[3],
                    'ip'        => $this->extractIPFromMessage($subMatches[3]),
                    'user_id'   => $this->extractUserIdFromMessage($subMatches[3]),
                ];
            }

            // Si no tiene source.level, usar 'system' como fuente por defecto
            return [
                'timestamp' => $matches[1],
                'source'    => 'system',
                'message'   => $message,
                'ip'        => $this->extractIPFromMessage($message),
                'user_id'   => $this->extractUserIdFromMessage($message),
            ];
        }

        // Patrón 3: Línea sin timestamp (usar timestamp actual)
        if (trim($line) && ! preg_match('/^\[/', $line)) {

            return [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'source'    => 'system',
                'message'   => $line,
                'ip'        => $this->extractIPFromMessage($line),
                'user_id'   => $this->extractUserIdFromMessage($line),
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

    // Métodos básicos del dashboard
    public function index()
    {
        try {
            $dashboardData = [
                'risk_distribution'  => $this->dashboardService->getRiskLevelDistribution(),
                'threats_by_country' => $this->dashboardService->getThreatsByCountry(),
                'top_suspicious_ips' => $this->dashboardService->getTopSuspiciousIPs(),
                'recent_events'      => $this->dashboardService->getRecentEvents(10),
                'threat_trends'      => $this->dashboardService->getThreatTrends(),
                'system_performance' => $this->dashboardService->getSystemPerformance(),
            ];

            return view('security.index', $dashboardData);
        } catch (\Exception $e) {
            return view('security.index', [
                'risk_distribution'  => [0, 0, 0],
                'threats_by_country' => [],
                'top_suspicious_ips' => collect(),
                'recent_events'      => collect(),
                'threat_trends'      => [],
                'system_performance' => [],
                'error_message'      => 'Error al cargar el dashboard de seguridad: ' . $e->getMessage(),
            ]);
        }
    }

    public function events()
    {
        try {
            // Obtener eventos de seguridad solo de hoy
            $events = SecurityEvent::whereDate('created_at', now()->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();

            // Calcular estadísticas
            $criticalEventsCount = $events->where('threat_score', '>=', 80)->count();
            $highEventsCount     = $events->whereBetween('threat_score', [60, 79])->count();
            $mediumEventsCount   = $events->whereBetween('threat_score', [40, 59])->count();
            $uniqueIPsCount      = $events->whereNotNull('ip_address')->pluck('ip_address')->unique()->count();
            $totalEventsCount    = $events->count();

            return view('security.events', [
                'events'              => $events,
                'criticalEventsCount' => $criticalEventsCount,
                'highEventsCount'     => $highEventsCount,
                'mediumEventsCount'   => $mediumEventsCount,
                'uniqueIPsCount'      => $uniqueIPsCount,
                'totalEventsCount'    => $totalEventsCount,
            ]);
        } catch (\Exception $e) {
            // En caso de error, retornar vista con datos vacíos
            return view('security.events', [
                'events'              => collect(),
                'criticalEventsCount' => 0,
                'highEventsCount'     => 0,
                'mediumEventsCount'   => 0,
                'uniqueIPsCount'      => 0,
                'totalEventsCount'    => 0,
                'error_message'       => 'Error al cargar eventos de seguridad: ' . $e->getMessage(),
            ]);
        }
    }

    public function threatIntelligence()
    {
        try {
            // Obtener amenazas de inteligencia de los últimos 3 días
            $threats = ThreatIntelligence::where('created_at', '>=', now()->subDays(3))
                ->orderBy('created_at', 'desc')
                ->get();

            // Si no hay datos en ThreatIntelligence, usar SecurityEvent como fallback
            if ($threats->isEmpty()) {
                $securityEvents = SecurityEvent::where('created_at', '>=', now()->subDays(3))
                    ->orderBy('created_at', 'desc')
                    ->get();

                $threats = $securityEvents->map(function ($event) {
                    $geolocation = is_string($event->geolocation)
                        ? json_decode($event->geolocation, true)
                        : $event->geolocation;

                    return (object) [
                        'id'                => $event->id,
                        'ip_address'        => $event->ip_address,
                        'ip'                => $event->ip_address,
                        'threat_type'       => $event->category,
                        'type'              => ucfirst(str_replace('_', ' ', $event->category)),
                        'classification'    => $this->mapRiskToClassification($event->risk_level),
                        'risk_level'        => $event->risk_level,
                        'threat_score'      => $event->threat_score,
                        'score'             => $event->threat_score,
                        'confidence'        => min(100, $event->threat_score + 20), // Simular confianza
                        'status'            => $event->outcome ?? 'active',
                        'country'           => $geolocation['country'] ?? 'Unknown',
                        'geographic_origin' => $geolocation['country'] ?? 'Unknown',
                        'malware_family'    => 'N/A',
                        'malwareFamily'     => 'N/A',
                        'attack_vectors'    => [$event->category],
                        'attackVectors'     => [$event->category],
                        'created_at'        => $event->created_at,
                        'updated_at'        => $event->updated_at,
                    ];
                });
            }

            // Calcular estadísticas basadas en classification mapeada a niveles de riesgo
            $totalThreats  = $threats->count();
            $activeThreats = $threats->where('status', 'active')->count();

            // Mapear clasificaciones existentes a niveles de riesgo
            $criticalThreats = $threats->whereIn('classification', ['malware', 'phishing'])->count();
            $highThreats     = $threats->whereIn('classification', ['ddos', 'sql_injection'])->count();
            $mediumThreats   = $threats->whereIn('classification', ['xss'])->count();

            // Datos de evolución (últimos 3 días) - Formato para Chart.js
            $serverEvolutionData = [
                'dates'    => [],
                'critical' => [],
                'high'     => [],
                'medium'   => [],
            ];

            // Generar datos de evolución para los últimos 3 días
            for ($i = 2; $i >= 0; $i--) {
                $date                           = now()->subDays($i)->format('M d'); // Formato corto para el gráfico
                $serverEvolutionData['dates'][] = $date;

                // Contar amenazas por nivel de riesgo para cada día basado en classification mapeada
                $dayStart = now()->subDays($i)->startOfDay();
                $dayEnd   = now()->subDays($i)->endOfDay();

                $dayThreats = ThreatIntelligence::whereBetween('created_at', [$dayStart, $dayEnd])->get();

                $serverEvolutionData['critical'][] = $dayThreats->whereIn('classification', ['malware', 'phishing'])->count();
                $serverEvolutionData['high'][]     = $dayThreats->whereIn('classification', ['ddos', 'sql_injection'])->count();
                $serverEvolutionData['medium'][]   = $dayThreats->whereIn('classification', ['xss'])->count();
            }

            // Obtener tipos de amenazas únicos para los filtros
            $threatTypes = $threats->pluck('threat_type')->unique()->filter()->mapWithKeys(function ($type) {
                return [$type => ucfirst(str_replace('_', ' ', $type))];
            })->toArray();

            return view('security.threat-intelligence', [
                'threats'             => $threats,
                'serverEvolutionData' => $serverEvolutionData,
                'totalThreats'        => $totalThreats,
                'activeThreats'       => $activeThreats,
                'criticalThreats'     => $criticalThreats,
                'highThreats'         => $highThreats,
                'mediumThreats'       => $mediumThreats,
                'threatTypes'         => $threatTypes,
            ]);
        } catch (\Exception $e) {
            // En caso de error, retornar vista con datos vacíos
            return view('security.threat-intelligence', [
                'threats'             => collect(),
                'serverEvolutionData' => [
                    'dates'    => [],
                    'critical' => [],
                    'high'     => [],
                    'medium'   => [],
                ],
                'totalThreats'        => 0,
                'activeThreats'       => 0,
                'criticalThreats'     => 0,
                'highThreats'         => 0,
                'mediumThreats'       => 0,
                'threatTypes'         => [],
                'error_message'       => 'Error al cargar inteligencia de amenazas: ' . $e->getMessage(),
            ]);
        }
    }

    public function ipReputation()
    {
        try {
            // Usar el servicio de seguridad para obtener datos reales
            $ipReputations       = $this->simpleSecurity->getIPReputations();
            $riskDistribution    = $this->simpleSecurity->getRiskDistribution();
            $countryDistribution = $this->simpleSecurity->getCountryDistribution();

            return view('security.ip-reputation', [
                'ipReputations'       => $ipReputations,
                'riskDistribution'    => $riskDistribution,
                'countryDistribution' => $countryDistribution,
                'availableCountries'  => $ipReputations->pluck('country')->unique()->values(),
                'totalIPs'            => $ipReputations->count(),
                'criticalIPs'         => $riskDistribution['critical'] ?? 0,
                'highIPs'             => $riskDistribution['high'] ?? 0,
                'mediumIPs'           => $riskDistribution['medium'] ?? 0,
            ]);
        } catch (\Exception $e) {
            // En caso de error, retornar vista con datos vacíos
            return view('security.ip-reputation', [
                'ipReputations'       => collect(),
                'riskDistribution'    => [],
                'countryDistribution' => [],
                'availableCountries'  => [],
                'totalIPs'            => 0,
                'criticalIPs'         => 0,
                'highIPs'             => 0,
                'mediumIPs'           => 0,
                'error_message'       => 'Error al cargar reputación de IPs: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Mapear nivel de riesgo a clasificación de amenaza
     */
    private function mapRiskToClassification($riskLevel)
    {
        switch (strtolower($riskLevel)) {
            case 'critical':
                return 'malware';
            case 'high':
                return 'sql_injection';
            case 'medium':
                return 'xss';
            default:
                return 'medium';
        }
    }
}
