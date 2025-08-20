<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence;
use App\Models\IPReputation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    /**
     * Dashboard principal de seguridad
     */
    public function index()
    {
        // Obtener datos para el dashboard
        $data = [
            'securityEventsCount' => SecurityEvent::count(),
            'activeThreatsCount' => ThreatIntelligence::where('status', 'active')->count(),
            'blockedIPsCount' => IPReputation::where('blacklisted', true)->count(),
            'recentEvents' => SecurityEvent::latest()->take(5)->get(),
            'topThreats' => ThreatIntelligence::orderBy('threat_score', 'desc')->take(5)->get(),
            'suspiciousIPs' => IPReputation::where('risk_level', 'high')->take(5)->get(),
        ];
        // Debug: Log de los datos
        Log::info('Dashboard Security Data:', $data);
        
        return view('security.index', $data);
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
        return view('security.ip-reputation');
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
                'duration' => 'nullable|integer|min:1|max:365'
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
                'blocked_by' => auth()->id()
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
                    'blocked_by' => auth()->id()
                ],
                'created_at' => now()
            ]);

            Log::info("IP {$ip} bloqueada manualmente por usuario " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "IP {$ip} bloqueada exitosamente por {$duration} horas",
                'data' => [
                    'ip' => $ip,
                    'expires_at' => now()->addHours($duration)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error al bloquear IP: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al bloquear IP: ' . $e->getMessage()
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
                'permanent' => 'boolean'
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
                'added_by' => auth()->id()
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
                    'added_by' => auth()->id()
                ],
                'created_at' => now()
            ]);

            Log::info("IP {$ip} agregada a whitelist por usuario " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "IP {$ip} agregada a whitelist exitosamente",
                'data' => [
                    'ip' => $ip,
                    'permanent' => $permanent
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error al agregar IP a whitelist: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar IP a whitelist: ' . $e->getMessage()
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
                'allowed_ips.*' => 'ip'
            ]);

            $enabled = $request->enabled;
            $message = $request->message ?? 'Sitio en mantenimiento. Volveremos pronto.';
            $allowedIPs = $request->allowed_ips ?? [];

            if ($enabled) {
                // Activar modo mantenimiento
                \Artisan::call('down', [
                    '--message' => $message,
                    '--retry' => 60,
                    '--secret' => 'admin-access-' . time()
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
                    'allowed_ips' => $allowedIPs
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error al cambiar modo mantenimiento: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar modo mantenimiento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = Cache::remember('security.dashboard_stats', 300, function () {
                return [
                    'total_events' => SecurityEvent::count(),
                    'critical_threats' => SecurityEvent::where('threat_score', '>=', 80)->count(),
                    'blocked_ips' => SecurityEvent::where('action_taken', 'block')->distinct('ip_address')->count(),
                    'prevention_rate' => $this->calculatePreventionRate(),
                    'recent_events' => SecurityEvent::latest()->take(10)->get(),
                    'top_suspicious_ips' => $this->getTopSuspiciousIPs(),
                    'threat_distribution' => $this->getThreatDistribution(),
                    'system_status' => $this->getSystemStatus()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener estadísticas del dashboard: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
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
                'data' => $events
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener eventos de seguridad: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener eventos'
            ], 500);
        }
    }

    /**
     * Calcular tasa de prevención
     */
    private function calculatePreventionRate(): float
    {
        $totalEvents = SecurityEvent::count();
        if ($totalEvents === 0) return 100.0;

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
                ->count()
        ];
    }
}
