<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use App\Services\SimpleSecurityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SecurityController extends Controller
{
    protected $simpleSecurity;

    public function __construct(SimpleSecurityService $simpleSecurity)
    {
        $this->simpleSecurity = $simpleSecurity;
    }
    /**
     * Dashboard principal de seguridad (SIMPLIFICADO)
     */
    public function index()
    {
        try {
            // Datos reales de la base de datos
            $data = [
                'securityEventsCount' => SecurityEvent::where('created_at', '>=', now()->subDay())->count(),
                'activeThreatsCount' => SecurityEvent::where('threat_score', '>=', 80)->where('created_at', '>=', now()->subDay())->count(),
                'blockedIPsCount' => $this->getBlockedIPsCount(),
                'recentEvents' => SecurityEvent::latest()->take(10)->get(),
                'suspiciousIPs' => $this->getSuspiciousIPs(),
                'riskLevelDistribution' => $this->getRiskLevelDistribution(),
                'threatsByCountry' => $this->getThreatsByCountry(),
            ];

            return view('security.index', $data);
        } catch (\Exception $e) {
            Log::error('Error en dashboard de seguridad: ' . $e->getMessage());

            // Datos por defecto en caso de error
            $data = [
                'securityEventsCount' => 0,
                'activeThreatsCount' => 0,
                'blockedIPsCount' => 0,
                'recentEvents' => collect(),
                'suspiciousIPs' => collect(),
                'riskLevelDistribution' => [0, 0, 0, 0, 0],
                'threatsByCountry' => [],
            ];

            return view('security.index', $data);
        }
    }

    /**
     * Obtener conteo de IPs bloqueadas
     */
    private function getBlockedIPsCount(): int
    {
        try {
            return SecurityEvent::where('action_taken', 'block')
                ->where('created_at', '>=', now()->subDay())
                ->count();
        } catch (\Exception $e) {
            Log::error('Error obteniendo IPs bloqueadas: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener IPs sospechosas
     */
    private function getSuspiciousIPs()
    {
        try {
            return SecurityEvent::select('ip_address')
                ->selectRaw('AVG(threat_score) as reputation_score')
                ->selectRaw('COUNT(*) as event_count')
                ->where('threat_score', '>=', 60)
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('ip_address')
                ->orderByDesc('reputation_score')
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error obteniendo IPs sospechosas: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtener distribución de eventos por nivel de riesgo
     */
    private function getRiskLevelDistribution(): array
    {
        try {
            $distribution = [
                'minimal' => SecurityEvent::where('threat_score', '<', 20)->where('created_at', '>=', now()->subDays(30))->count(),
                'low' => SecurityEvent::whereBetween('threat_score', [20, 39])->where('created_at', '>=', now()->subDays(30))->count(),
                'medium' => SecurityEvent::whereBetween('threat_score', [40, 59])->where('created_at', '>=', now()->subDays(30))->count(),
                'high' => SecurityEvent::whereBetween('threat_score', [60, 79])->where('created_at', '>=', now()->subDays(30))->count(),
                'critical' => SecurityEvent::where('threat_score', '>=', 80)->where('created_at', '>=', now()->subDays(30))->count(),
            ];

            return array_values($distribution);
        } catch (\Exception $e) {
            Log::error('Error obteniendo distribución de riesgo: ' . $e->getMessage());
            return [0, 0, 0, 0, 0]; // ← AQUÍ ESTÁ EL PROBLEMA
        }
    }

    /**
     * Obtener amenazas por país
     */
    private function getThreatsByCountry(): array
    {
        try {
            // Obtener datos de IPs por país desde ip_reputations usando DB directamente
            if (Schema::hasTable('ip_reputations')) {
                $threatsByCountry = DB::table('ip_reputations')
                    ->selectRaw('
                        JSON_UNQUOTE(JSON_EXTRACT(geographic_data, "$.country")) as country,
                        COUNT(*) as count
                    ')
                    ->whereNotNull('geographic_data')
                    ->groupBy('country')
                    ->orderByDesc('count')
                    ->take(5)
                    ->pluck('count', 'country')
                    ->toArray();

                return $threatsByCountry;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error obteniendo amenazas por país: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Vista de eventos de seguridad
     */
    public function events()
    {
        $events = SecurityEvent::with(['user'])
            ->latest()
            ->paginate(20);

        return view('security.events', compact('events'));
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
     * Vista de configuración de seguridad
     */
    public function settings()
    {
        return view('security.settings');
    }

    /**
     * Vista de reportes de seguridad
     */
    public function reports()
    {
        return view('security.reports');
    }

    /**
     * Vista de logs de seguridad
     */
    public function logs()
    {
        return view('security.logs');
    }

    /**
     * Bloquear una IP
     */
    public function blockIP(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ip' => 'required|ip',
                'reason' => 'nullable|string|max:500',
                'duration' => 'nullable|integer|min:1|max:365',
            ]);

            $ip = $request->ip;
            $reason = $request->reason ?? 'Bloqueo manual por administrador';
            $duration = $request->duration ?? 24; // horas por defecto

            // Agregar IP a blacklist
            $blacklist = Cache::get('security.blacklist', []);
            $blacklist[$ip] = [
                'reason' => $reason,
                'blocked_at' => now(),
                'expires_at' => now()->addHours($duration),
                'blocked_by' => auth()->id(),
            ];
            Cache::put('security.blacklist', $blacklist, now()->addDays(30));

            // Registrar evento de seguridad
            SecurityEvent::create([
                'ip_address' => $ip,
                'event_type' => 'manual_block',
                'threat_score' => 100,
                'action_taken' => 'block',
                'details' => [
                    'reason' => $reason,
                    'duration_hours' => $duration,
                    'blocked_by' => auth()->id(),
                ],
                'created_at' => now(),
            ]);

            Log::info("IP {$ip} bloqueada manualmente por usuario " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "IP {$ip} bloqueada exitosamente por {$duration} horas",
                'data' => [
                    'ip' => $ip,
                    'expires_at' => now()->addHours($duration)->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("Error al bloquear IP: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al bloquear IP: ' . $e->getMessage(),
            ], 500);
        }
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
                'action_taken' => 'whitelist',
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
     * Activar/Desactivar modo mantenimiento
     */
    public function toggleMaintenance(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'enabled' => 'required|boolean',
                'message' => 'nullable|string|max:500',
                'allowed_ips' => 'nullable|array',
                'allowed_ips.*' => 'ip',
            ]);

            $enabled = $request->enabled;
            $message = $request->message ?? 'Sitio en mantenimiento. Volveremos pronto.';
            $allowedIPs = $request->allowed_ips ?? [];

            if ($enabled) {
                // Activar modo mantenimiento
                \Artisan::call('down', [
                    '--message' => $message,
                    '--retry' => 60,
                    '--secret' => 'admin-access-' . time(),
                ]);

                // Guardar configuración de mantenimiento
                Cache::put('maintenance.enabled', true, now()->addDays(30));
                Cache::put('maintenance.message', $message, now()->addDays(30));
                Cache::put('maintenance.allowed_ips', $allowedIPs, now()->addDays(30));
                Cache::put('maintenance.activated_by', auth()->id(), now()->addDays(30));
                Cache::put('maintenance.activated_at', now(), now()->addDays(30));

                Log::info("Modo mantenimiento activado por usuario " . auth()->id());
            } else {
                // Desactivar modo mantenimiento
                \Artisan::call('up');

                // Limpiar configuración de mantenimiento
                Cache::forget('maintenance.enabled');
                Cache::forget('maintenance.message');
                Cache::forget('maintenance.allowed_ips');
                Cache::forget('maintenance.activated_by');
                Cache::forget('maintenance.activated_at');

                Log::info("Modo mantenimiento desactivado por usuario " . auth()->id());
            }

            return response()->json([
                'success' => true,
                'message' => $enabled ? 'Modo mantenimiento activado' : 'Modo mantenimiento desactivado',
                'data' => [
                    'enabled' => $enabled,
                    'message' => $message,
                    'allowed_ips' => $allowedIPs,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("Error al cambiar modo mantenimiento: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar modo mantenimiento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Estadísticas del dashboard (SIMPLIFICADAS)
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = Cache::remember('security.dashboard_stats', 3600, function () {
                return [
                    'total_events_24h' => SecurityEvent::where('created_at', '>=', now()->subDay())->count(),
                    'critical_threats_24h' => SecurityEvent::where('threat_score', '>=', 80)->where('created_at', '>=', now()->subDay())->count(),
                    'recent_events' => SecurityEvent::latest()->take(10)->get(['ip_address', 'threat_score', 'created_at', 'action_taken']),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

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

            if ($request->filled('action_taken')) {
                $query->where('action_taken', $request->action_taken);
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

        $preventedEvents = SecurityEvent::whereIn('action_taken', ['block', 'challenge', 'rate_limit'])->count();
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
    public function getEventsData()
    {
        try {
            $events = SecurityEvent::select(
                'id',
                'ip_address',
                'category',
                'threat_score',
                'action_taken',
                'created_at'
            )
                ->latest()
                ->take(50)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'ip' => $event->ip_address,
                        'score' => $event->threat_score,
                        'risk_level' => $this->getRiskLevelFromScore($event->threat_score),
                        'category' => $event->category ?? 'unknown',
                        'action' => $event->action_taken ?? 'monitor',
                        'date' => $event->created_at->format('d/m/Y, H:i'),
                        'status' => $this->getStatusFromAction($event->action_taken),
                        'country' => 'N/A',
                        'city' => 'N/A',
                        'reason' => 'Evento de seguridad detectado',
                    ];
                });

            return response()->json([
                'success' => true,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos de eventos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'events' => [],
                'message' => 'Error al cargar eventos',
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

    /**
     * Obtener status desde acción
     */
    private function getStatusFromAction($action)
    {
        switch ($action) {
            case 'block':
                return 'resolved';
            case 'challenge':
                return 'investigating';
            case 'monitor':
                return 'open';
            default:
                return 'open';
        }
    }
}
