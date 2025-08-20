<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SecurityAlert;
use App\Models\SecurityEvent;
use App\Services\ThreatIntelligenceService;
use App\Services\AnomalyDetectionService;
use App\Services\IPReputationService;

class SecurityMonitoring
{
    protected $threatIntelligence;
    protected $anomalyDetection;
    protected $ipReputation;
    
    // Configuración de umbrales de seguridad
    protected $config = [
        'max_requests_per_minute' => 100,
        'max_failed_logins' => 5,
        'suspicious_patterns' => [
            'sql_injection' => ['/union\s+select/i', '/drop\s+table/i', '/--/'],
            'xss_attack' => ['<script', 'javascript:', 'onload='],
            'path_traversal' => ['../', '..\\', '%2e%2e%2f'],
            'command_injection' => [';', '|', '&&', '`'],
        ],
        'high_risk_ips' => [],
        'whitelist_ips' => [],
        'alert_channels' => ['email', 'slack', 'webhook']
    ];

    public function __construct(
        ThreatIntelligenceService $threatIntelligence,
        AnomalyDetectionService $anomalyDetection,
        IPReputationService $ipReputation
    ) {
        $this->threatIntelligence = $threatIntelligence;
        $this->anomalyDetection = $anomalyDetection;
        $this->ipReputation = $ipReputation;
    }

    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        try {
            // 1. ANÁLISIS DE REPUTACIÓN DE IP EN TIEMPO REAL
            $ipAnalysis = $this->analyzeIPReputation($request);
            
            // 2. DETECCIÓN DE PATRONES DE ATAQUE
            $threatScore = $this->detectThreatPatterns($request);
            
            // 3. ANÁLISIS DE COMPORTAMIENTO ANÓMALO
            $anomalyScore = $this->detectAnomalies($request);
            
            // 4. CORRELACIÓN DE EVENTOS DE SEGURIDAD
            $correlationScore = $this->correlateSecurityEvents($request);
            
            // 5. CÁLCULO DE SCORE DE AMENAZA TOTAL
            $totalThreatScore = $this->calculateTotalThreatScore([
                'ip_reputation' => $ipAnalysis['score'],
                'threat_patterns' => $threatScore,
                'anomaly_detection' => $anomalyScore,
                'event_correlation' => $correlationScore
            ]);
            
            // 6. TOMA DE DECISIÓN INTELIGENTE
            $action = $this->determineSecurityAction($totalThreatScore, $request);
            
            // 7. REGISTRO Y ALERTAS
            $this->logSecurityEvent($request, $totalThreatScore, $action);
            
            // 8. EJECUCIÓN DE ACCIONES DE SEGURIDAD
            $this->executeSecurityActions($action, $request);
            
            // 9. MONITOREO DE PERFORMANCE
            $this->monitorPerformance($startTime);
            
            // 10. CONTINUAR CON LA REQUEST SI NO HAY AMENAZAS
            if ($action['type'] === 'allow') {
                return $next($request);
            }
            
            // 11. RESPUESTA DE SEGURIDAD PERSONALIZADA
            return $this->generateSecurityResponse($action, $request);
            
        } catch (\Exception $e) {
            Log::error('Security Monitoring Error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'ip' => $request->ip()
            ]);
            
            // En caso de error, permitir la request pero registrar
            return $next($request);
        }
    }

    /**
     * Análisis de reputación de IP en tiempo real
     */
    protected function analyzeIPReputation(Request $request): array
    {
        $ip = $request->ip();
        
        // Verificar whitelist
        if (in_array($ip, $this->config['whitelist_ips'])) {
            return ['score' => 0, 'risk' => 'low', 'reason' => 'whitelisted'];
        }
        
        // Verificar blacklist
        if (in_array($ip, $this->config['high_risk_ips'])) {
            return ['score' => 100, 'risk' => 'critical', 'reason' => 'blacklisted'];
        }
        
        // Consultar servicio de reputación de IP
        $reputation = $this->ipReputation->checkReputation($ip);
        
        // Análisis de comportamiento histórico
        $historicalData = $this->analyzeHistoricalBehavior($ip);
        
        // Machine Learning: Predicción de riesgo
        $mlRiskScore = $this->anomalyDetection->predictRisk($ip, $historicalData);
        
        return [
            'score' => $reputation['score'] + $mlRiskScore,
            'risk' => $this->categorizeRisk($reputation['score'] + $mlRiskScore),
            'reason' => $reputation['reason'],
            'ml_score' => $mlRiskScore
        ];
    }

    /**
     * Detección de patrones de ataque usando regex y ML
     */
    protected function detectThreatPatterns(Request $request): float
    {
        $threatScore = 0;
        $payload = json_encode($request->all()) . $request->getContent();
        
        foreach ($this->config['suspicious_patterns'] as $attackType => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $payload)) {
                    $threatScore += $this->getThreatWeight($attackType);
                    
                    // Análisis de contexto para reducir falsos positivos
                    if ($this->isFalsePositive($request, $attackType)) {
                        $threatScore *= 0.3; // Reducir score si es falso positivo
                    }
                }
            }
        }
        
        // Machine Learning: Análisis de payload
        $mlThreatScore = $this->anomalyDetection->analyzePayload($payload);
        
        return min(100, $threatScore + $mlThreatScore);
    }

    /**
     * Detección de anomalías usando Machine Learning
     */
    protected function detectAnomalies(Request $request): float
    {
        $features = [
            'request_frequency' => $this->getRequestFrequency($request->ip()),
            'user_agent_anomaly' => $this->detectUserAgentAnomaly($request),
            'geographic_anomaly' => $this->detectGeographicAnomaly($request),
            'time_pattern_anomaly' => $this->detectTimePatternAnomaly($request),
            'session_anomaly' => $this->detectSessionAnomaly($request),
            'resource_anomaly' => $this->detectResourceAnomaly($request)
        ];
        
        // Machine Learning: Predicción de anomalía
        return $this->anomalyDetection->predictAnomaly($features);
    }

    /**
     * Correlación de eventos de seguridad
     */
    protected function correlateSecurityEvents(Request $request): float
    {
        $ip = $request->ip();
        $timeWindow = now()->subMinutes(30);
        
        // Obtener eventos recientes
        $recentEvents = SecurityEvent::where('ip_address', $ip)
            ->where('created_at', '>=', $timeWindow)
            ->get();
        
        $correlationScore = 0;
        
        // Análisis de secuencia de eventos
        if ($recentEvents->count() > 0) {
            $correlationScore += $this->analyzeEventSequence($recentEvents);
        }
        
        // Análisis de patrones temporales
        $correlationScore += $this->analyzeTemporalPatterns($recentEvents);
        
        // Análisis de correlación con amenazas globales
        $correlationScore += $this->analyzeGlobalThreatCorrelation($request);
        
        return min(100, $correlationScore);
    }

    /**
     * Cálculo de score de amenaza total usando pesos inteligentes
     */
    protected function calculateTotalThreatScore(array $scores): float
    {
        $weights = [
            'ip_reputation' => 0.25,
            'threat_patterns' => 0.35,
            'anomaly_detection' => 0.25,
            'event_correlation' => 0.15
        ];
        
        $totalScore = 0;
        foreach ($scores as $type => $score) {
            $totalScore += $score * $weights[$type];
        }
        
        // Aplicar factor de ajuste dinámico
        $adjustmentFactor = $this->calculateAdjustmentFactor($scores);
        
        return min(100, $totalScore * $adjustmentFactor);
    }

    /**
     * Determinación de acción de seguridad basada en score
     */
    protected function determineSecurityAction(float $threatScore, Request $request): array
    {
        if ($threatScore >= 80) {
            return [
                'type' => 'block',
                'action' => 'immediate_block',
                'duration' => '24h',
                'reason' => 'Critical threat detected'
            ];
        } elseif ($threatScore >= 60) {
            return [
                'type' => 'challenge',
                'action' => 'captcha_challenge',
                'duration' => '1h',
                'reason' => 'High risk activity detected'
            ];
        } elseif ($threatScore >= 40) {
            return [
                'type' => 'monitor',
                'action' => 'enhanced_monitoring',
                'duration' => '30m',
                'reason' => 'Suspicious activity detected'
            ];
        } else {
            return [
                'type' => 'allow',
                'action' => 'normal_processing',
                'duration' => null,
                'reason' => 'Low risk activity'
            ];
        }
    }

    /**
     * Registro de eventos de seguridad
     */
    protected function logSecurityEvent(Request $request, float $threatScore, array $action): void
    {
        $event = SecurityEvent::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_uri' => $request->getRequestUri(),
            'request_method' => $request->method(),
            'threat_score' => $threatScore,
            'action_taken' => $action['action'],
            'reason' => $action['reason'],
            'payload' => $this->sanitizePayload($request->all()),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
            'geolocation' => $this->getGeolocation($request->ip()),
            'risk_level' => $this->categorizeRisk($threatScore)
        ]);
        
        // Enviar alertas si es necesario
        if ($threatScore >= 60) {
            $this->sendSecurityAlert($event);
        }
    }

    /**
     * Ejecución de acciones de seguridad
     */
    protected function executeSecurityActions(array $action, Request $request): void
    {
        $ip = $request->ip();
        
        switch ($action['action']) {
            case 'immediate_block':
                Cache::put("blocked_ip_{$ip}", true, now()->addHours(24));
                $this->updateFirewallRules($ip, 'block');
                break;
                
            case 'captcha_challenge':
                Cache::put("challenge_ip_{$ip}", true, now()->addHour());
                break;
                
            case 'enhanced_monitoring':
                Cache::put("monitor_ip_{$ip}", true, now()->addMinutes(30));
                break;
        }
    }

    /**
     * Generación de respuesta de seguridad personalizada
     */
    protected function generateSecurityResponse(array $action, Request $request)
    {
        switch ($action['action']) {
            case 'immediate_block':
                return response()->json([
                    'error' => 'Access denied',
                    'reason' => 'Security policy violation',
                    'incident_id' => uniqid('SEC-'),
                    'contact' => config('security.contact_email')
                ], 403);
                
            case 'captcha_challenge':
                return response()->json([
                    'error' => 'Security challenge required',
                    'challenge_type' => 'captcha',
                    'session_id' => session()->getId()
                ], 429);
                
            default:
                return response()->json([
                    'error' => 'Access temporarily restricted',
                    'reason' => $action['reason']
                ], 429);
        }
    }

    /**
     * Métodos auxiliares
     */
    protected function categorizeRisk(float $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'minimal';
    }

    protected function getThreatWeight(string $attackType): float
    {
        $weights = [
            'sql_injection' => 25,
            'xss_attack' => 20,
            'path_traversal' => 15,
            'command_injection' => 30
        ];
        
        return $weights[$attackType] ?? 10;
    }

    protected function sanitizePayload(array $payload): array
    {
        // Sanitizar datos sensibles
        $sensitiveKeys = ['password', 'token', 'secret', 'key'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($payload[$key])) {
                $payload[$key] = '***REDACTED***';
            }
        }
        
        return $payload;
    }

    protected function sanitizeHeaders(array $headers): array
    {
        // Sanitizar headers sensibles
        $sensitiveHeaders = ['authorization', 'cookie', 'x-csrf-token'];
        
        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = '***REDACTED***';
            }
        }
        
        return $headers;
    }

    protected function sendSecurityAlert(SecurityEvent $event): void
    {
        // Enviar notificaciones por múltiples canales
        foreach ($this->config['alert_channels'] as $channel) {
            try {
                switch ($channel) {
                    case 'email':
                        Notification::route('mail', config('security.alert_email'))
                            ->notify(new SecurityAlert($event));
                        break;
                        
                    case 'slack':
                        $this->sendSlackAlert($event);
                        break;
                        
                    case 'webhook':
                        $this->sendWebhookAlert($event);
                        break;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send security alert via {$channel}", [
                    'error' => $e->getMessage(),
                    'event_id' => $event->id
                ]);
            }
        }
    }

    protected function monitorPerformance(float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;
        
        // Registrar métricas de performance
        if ($executionTime > 0.1) { // Más de 100ms
            Log::warning('Security monitoring performance issue', [
                'execution_time' => $executionTime,
                'memory_usage' => memory_get_usage(true)
            ]);
        }
    }

    /**
     * Análisis de comportamiento histórico de una IP
     */
    protected function analyzeHistoricalBehavior(string $ip): array
    {
        try {
            // Obtener eventos históricos de los últimos 7 días
            $timeWindow = now()->subDays(7);
            
            $historicalEvents = SecurityEvent::where('ip_address', $ip)
                ->where('created_at', '>=', $timeWindow)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($historicalEvents->isEmpty()) {
                return [
                    'total_events' => 0,
                    'threat_score_avg' => 0,
                    'risk_level' => 'low',
                    'behavior_pattern' => 'normal',
                    'last_activity' => null
                ];
            }

            // Calcular métricas históricas
            $totalEvents = $historicalEvents->count();
            $threatScoreAvg = $historicalEvents->avg('threat_score') ?? 0;
            $riskLevel = $this->categorizeRisk($threatScoreAvg);
            
            // Determinar patrón de comportamiento
            $behaviorPattern = $this->determineBehaviorPattern($historicalEvents);
            
            // Última actividad
            $lastActivity = $historicalEvents->first()->created_at;

            return [
                'total_events' => $totalEvents,
                'threat_score_avg' => round($threatScoreAvg, 2),
                'risk_level' => $riskLevel,
                'behavior_pattern' => $behaviorPattern,
                'last_activity' => $lastActivity
            ];

        } catch (\Exception $e) {
            Log::error('Historical Behavior Analysis Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);

            // Retornar datos por defecto en caso de error
            return [
                'total_events' => 0,
                'threat_score_avg' => 0,
                'risk_level' => 'low',
                'behavior_pattern' => 'unknown',
                'last_activity' => null
            ];
        }
    }

    /**
     * Determinar patrón de comportamiento basado en eventos históricos
     */
    protected function determineBehaviorPattern($events): string
    {
        if ($events->isEmpty()) return 'normal';

        $eventTypes = $events->pluck('event_type')->countBy();
        $threatScores = $events->pluck('threat_score')->toArray();
        
        // Análisis de frecuencia
        $avgThreatScore = array_sum($threatScores) / count($threatScores);
        $highThreatEvents = count(array_filter($threatScores, fn($score) => $score > 70));
        
        if ($highThreatEvents > count($events) * 0.3) {
            return 'aggressive';
        } elseif ($avgThreatScore > 50) {
            return 'suspicious';
        } elseif ($avgThreatScore < 20) {
            return 'benign';
        }
        
        return 'normal';
    }
}
