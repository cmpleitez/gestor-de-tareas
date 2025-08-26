<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence;
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
use App\Services\GeolocationService;
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
     * Vista de logs de seguridad - VERSIÓN SIMPLIFICADA
     */
    public function logs(Request $request)
    {
        try {
            // Parámetros de paginación
            $perPage = $request->get('per_page', 25);
            $page = $request->get('page', 1);

            // Filtros
            $source = $request->get('source');
            $search = $request->get('search');
            $ip = $request->get('ip');
            $date = $request->get('date');

            // Leer logs reales del sistema de seguridad (EXCLUYENDO laravel.log para evitar loop infinito)
            $logs = collect();
            $filesToRead = [];

            // Definir archivos de logs de seguridad a leer
            $logFiles = [
                'security' => storage_path('logs/security.log'),
                'firewall' => storage_path('logs/firewall.log'),
                'ids' => storage_path('logs/ids.log')
            ];

            // Leer cada archivo de log si existe
            foreach ($logFiles as $logSource => $filePath) {
                if (file_exists($filePath) && is_readable($filePath)) {
                    $fileToRead = $logSource;
                    $filesToRead[] = $fileToRead;

                    try {
                        $fileContent = file_get_contents($filePath);
                        if ($fileContent) {
                            $lines = explode("\n", $fileContent);

                            foreach ($lines as $line) {
                                $line = trim($line);
                                if (empty($line))
                                    continue;

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
                    if ($search && !str_contains(strtolower($logEntry['message']), strtolower($search))) {
                        return false;
                    }

                    // Filtro por IP
                    if ($ip && !str_contains($logEntry['ip'], $ip)) {
                        return false;
                    }

                    // Filtro por fecha
                    if ($date && !str_starts_with($logEntry['timestamp'], $date)) {
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
                'per_page' => (int) $perPage,
                'total' => $totalLogs,
                'last_page' => ceil($totalLogs / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $totalLogs),
            ];




            // DEBUG: Para depuración, descomenta la siguiente línea:
            // dd($logs->values()->toArray(), $pagination);


            // Asegurar que logs sea siempre un array válido
            $logsArray = $logs->isNotEmpty() ? $logs->values()->toArray() : [];

            // Filtrar elementos vacíos o nulos
            $logsArray = array_filter($logsArray, function ($log) {
                return !empty($log) && is_array($log) && !empty($log['message']);
            });

            // Reindexar el array después del filtrado
            $logsArray = array_values($logsArray);

            // Validación final antes de enviar a la vista
            if (!is_array($logsArray)) {
                $logsArray = [];
            }

            // DEBUG: Verificar el tipo de datos antes de enviar
            // dd('DEBUG: $logsArray antes de enviar:', $logsArray, 'Tipo:', gettype($logsArray), 'Count:', is_array($logsArray) ? count($logsArray) : 'N/A');

            return view('security.logs', [
                'logs' => $logsArray,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            // Error silencioso para evitar loop infinito
            return view('security.logs', [
                'logs' => [],
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
     * Parsear línea de log de múltiples formatos
     */
    private function parseLogLine(string $line): ?array
    {
        // DEBUG: Log de la línea que se está parseando (COMENTADO PARA EVITAR LOOP)
        // Log::debug("Parseando línea: " . substr($line, 0, 100));

        // Patrón 1: [timestamp] source.level: mensaje (formato Laravel estándar)
        if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
            // Log::debug("✅ Patrón 1 exitoso: " . $matches[2] . "." . $matches[3]);
            return [
                'timestamp' => $matches[1],
                'source' => $matches[2],
                'message' => $matches[4],
                'ip' => $this->extractIPFromMessage($matches[4]),
                'user_id' => $this->extractUserIdFromMessage($matches[4]),
            ];
        }

        // Patrón 2: [timestamp] mensaje (formato genérico)
        if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (.+)$/', $line, $matches)) {
            $message = $matches[2];

            // Intentar extraer source.level del mensaje
            if (preg_match('/^(\w+)\.(\w+): (.+)$/', $message, $subMatches)) {
                // Log::debug("✅ Patrón 2 exitoso: " . $subMatches[1] . "." . $subMatches[2]);
                return [
                    'timestamp' => $matches[1],
                    'source' => $subMatches[1],
                    'message' => $subMatches[3],
                    'ip' => $this->extractIPFromMessage($subMatches[3]),
                    'user_id' => $this->extractUserIdFromMessage($subMatches[3]),
                ];
            }

            // Si no tiene source.level, usar 'system' como fuente por defecto
            // Log::debug("✅ Patrón 2 genérico: usando 'system' como fuente");
            return [
                'timestamp' => $matches[1],
                'source' => 'system',
                'message' => $message,
                'ip' => $this->extractIPFromMessage($message),
                'user_id' => $this->extractUserIdFromMessage($message),
            ];
        }

        // Patrón 3: Línea sin timestamp (usar timestamp actual)
        if (trim($line) && !preg_match('/^\[/', $line)) {
            // Log::debug("✅ Patrón 3: línea sin timestamp, usando timestamp actual");
            return [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'source' => 'system',
                'message' => $line,
                'ip' => $this->extractIPFromMessage($line),
                'user_id' => $this->extractUserIdFromMessage($line),
            ];
        }

        // Log::debug("❌ Ningún patrón coincide");
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
                'risk_distribution' => $this->dashboardService->getRiskLevelDistribution(),
                'threats_by_country' => $this->dashboardService->getThreatsByCountry(),
                'top_suspicious_ips' => $this->dashboardService->getTopSuspiciousIPs(),
                'recent_events' => $this->dashboardService->getRecentEvents(10),
                'threat_trends' => $this->dashboardService->getThreatTrends(),
                'system_performance' => $this->dashboardService->getSystemPerformance(),
            ];

            return view('security.index', $dashboardData);
        } catch (\Exception $e) {
            Log::error('Error en dashboard de seguridad: ' . $e->getMessage());
            return view('security.index', [
                'risk_distribution' => [0, 0, 0],
                'threats_by_country' => [],
                'top_suspicious_ips' => collect(),
                'recent_events' => collect(),
                'threat_trends' => [],
                'system_performance' => [],
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
            $highEventsCount = $events->whereBetween('threat_score', [60, 79])->count();
            $mediumEventsCount = $events->whereBetween('threat_score', [40, 59])->count();
            $uniqueIPsCount = $events->whereNotNull('ip_address')->pluck('ip_address')->unique()->count();
            $totalEventsCount = $events->count();

            Log::info("Eventos cargados: {$totalEventsCount}, Críticos: {$criticalEventsCount}, Altos: {$highEventsCount}, Medios: {$mediumEventsCount}, IPs únicas: {$uniqueIPsCount}");

            return view('security.events', [
                'events' => $events,
                'criticalEventsCount' => $criticalEventsCount,
                'highEventsCount' => $highEventsCount,
                'mediumEventsCount' => $mediumEventsCount,
                'uniqueIPsCount' => $uniqueIPsCount,
                'totalEventsCount' => $totalEventsCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en events: ' . $e->getMessage());

            // En caso de error, retornar vista con datos vacíos
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

    public function threatIntelligence()
    {
        try {
            // Obtener amenazas de inteligencia de los últimos 3 días
            $threats = ThreatIntelligence::where('created_at', '>=', now()->subDays(3))
                ->orderBy('created_at', 'desc')
                ->get();

            // Calcular estadísticas
            $totalThreats = $threats->count();
            $activeThreats = $threats->where('status', 'active')->count();
            $criticalThreats = $threats->where('classification', 'critical')->count();
            $highThreats = $threats->where('classification', 'high')->count();

            // Log para debugging
            Log::info("Estadísticas de amenazas - Total: {$totalThreats}, Activas: {$activeThreats}, Críticas: {$criticalThreats}, Altas: {$highThreats}");

            // Datos de evolución (últimos 3 días) - Formato para Chart.js
            $serverEvolutionData = [
                'dates' => [],
                'critical' => [],
                'high' => [],
                'medium' => []
            ];

            // Log para debugging
            Log::info('Generando datos de evolución para threat-intelligence');

            for ($i = 2; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('M d'); // Formato corto para el gráfico
                $serverEvolutionData['dates'][] = $date;

                // Contar amenazas por nivel de riesgo para cada día
                $dayStart = now()->subDays($i)->startOfDay();
                $dayEnd = now()->subDays($i)->endOfDay();

                $serverEvolutionData['critical'][] = ThreatIntelligence::where('classification', 'critical')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $serverEvolutionData['high'][] = ThreatIntelligence::where('classification', 'high')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();

                $serverEvolutionData['medium'][] = ThreatIntelligence::where('classification', 'medium')
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();
            }

            // Log para debugging
            Log::info('Datos de evolución generados:', $serverEvolutionData);

            return view('security.threat-intelligence', [
                'threats' => $threats,
                'serverEvolutionData' => $serverEvolutionData,
                'totalThreats' => $totalThreats,
                'activeThreats' => $activeThreats,
                'criticalThreats' => $criticalThreats,
                'highThreats' => $highThreats,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en threatIntelligence: ' . $e->getMessage());

            // En caso de error, retornar vista con datos vacíos
            return view('security.threat-intelligence', [
                'threats' => collect(),
                'serverEvolutionData' => [],
                'totalThreats' => 0,
                'activeThreats' => 0,
                'criticalThreats' => 0,
                'highThreats' => 0,
            ]);
        }
    }

    public function ipReputation()
    {
        try {
            // Usar el servicio de seguridad para obtener datos reales
            $ipReputations = $this->simpleSecurity->getIPReputations();
            $riskDistribution = $this->simpleSecurity->getRiskDistribution();
            $countryDistribution = $this->simpleSecurity->getCountryDistribution();

            return view('security.ip-reputation', [
                'ipReputations' => $ipReputations,
                'riskDistribution' => $riskDistribution,
                'countryDistribution' => $countryDistribution,
                'availableCountries' => $ipReputations->pluck('country')->unique()->values(),
                'totalIPs' => $ipReputations->count(),
                'criticalIPs' => $riskDistribution['critical'] ?? 0,
                'highIPs' => $riskDistribution['high'] ?? 0,
                'mediumIPs' => $riskDistribution['medium'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ipReputation: ' . $e->getMessage());

            // En caso de error, retornar vista con datos vacíos
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
}
