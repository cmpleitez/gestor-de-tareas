<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\SecurityEvent;
use App\Services\SimpleSecurityService;

class SecurityMonitoringOptimized
{
    protected $simpleSecurity;
    
    // Configuración mínima y eficiente
    protected $config = [
        'max_requests_per_hour' => 100,
        'critical_threat_score' => 80,
        'high_threat_score' => 60,
        'cache_duration' => 3600, // 1 hora
    ];

    public function __construct(SimpleSecurityService $simpleSecurity)
    {
        $this->simpleSecurity = $simpleSecurity;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            // CAPA 1: Escaneo rápido sin consultas a BD
            $threatScore = $this->quickThreatScan($request);
            
            // CAPA 2: Análisis profundo solo si es necesario
            if ($threatScore >= 60) {
                $detailedScore = $this->detailedThreatAnalysis($request);
                
                if ($detailedScore >= 80) {
                    $this->logCriticalEvent($request, $detailedScore);
                    return $this->generateBlockResponse();
                }
            }
            
            // Continuar normalmente (99% de los casos)
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Security Monitoring Error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            // En caso de error, permitir la request
            return $next($request);
        }
    }

    /**
     * CAPA 1: Escaneo rápido sin consultas a BD
     */
    protected function quickThreatScan(Request $request): float
    {
        $threatScore = 0;
        $payload = $request->getContent() . json_encode($request->all());
        
        // Patrones críticos simples
        $criticalPatterns = [
            'sql_injection' => ['/union\s+select/i', '/drop\s+table/i', '/--/'],
            'xss_attack' => ['<script', 'javascript:', 'onload='],
            'path_traversal' => ['../', '..\\', '%2e%2e%2f'],
            'command_injection' => [';', '|', '&&', '`']
        ];
        
        foreach ($criticalPatterns as $attackType => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $payload)) {
                    $threatScore += $this->getThreatWeight($attackType);
                }
            }
        }
        
        return min(100, $threatScore);
    }

    /**
     * CAPA 2: Análisis profundo solo cuando es necesario
     */
    protected function detailedThreatAnalysis(Request $request): float
    {
        $ip = $request->ip();
        
        // Usar el servicio simple de seguridad
        $threatAnalysis = $this->simpleSecurity->analyzeThreats($ip);
        $patternScore = $this->quickThreatScan($request);
        
        // Score total: máximo entre análisis de comportamiento y patrones
        $totalScore = max($threatAnalysis['total_score'], $patternScore);
        
        return $totalScore;
    }

    /**
     * Solo registrar eventos críticos
     */
    protected function logCriticalEvent(Request $request, float $threatScore): void
    {
        SecurityEvent::create([
            'ip_address' => $request->ip(),
            'request_uri' => $request->getRequestUri(),
            'request_method' => $request->method(),
            'threat_score' => $threatScore,
            'action_taken' => 'block',
            'reason' => 'Critical threat detected',
            'risk_level' => $this->categorizeRisk($threatScore)
        ]);
    }

    /**
     * Respuesta de bloqueo
     */
    protected function generateBlockResponse()
    {
        return response()->json([
            'error' => 'Access denied',
            'reason' => 'Security policy violation',
            'incident_id' => uniqid('SEC-'),
            'contact' => config('security.contact_email', 'admin@example.com')
        ], 403);
    }

    /**
     * Métodos auxiliares
     */
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

    protected function categorizeRisk(float $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'minimal';
    }
}
