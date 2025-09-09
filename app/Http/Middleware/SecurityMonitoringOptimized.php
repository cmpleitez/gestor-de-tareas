<?php
namespace App\Http\Middleware;

use App\Models\SecurityEvent;
// Revertido: no importar ThreatIntelligence para evitar escrituras directas desde middleware
use App\Services\GeolocationService;
use App\Services\SimpleSecurityService;
use Closure;
use Illuminate\Http\Request;

class SecurityMonitoringOptimized
{
    protected $simpleSecurity;
    protected $geolocationService;

    // Configuración mínima y eficiente
    protected $config = [
        'max_requests_per_hour' => 100,
        'critical_threat_score' => 80,
        'high_threat_score'     => 60,
        'cache_duration'        => 3600, // 1 hora
    ];

    public function __construct(SimpleSecurityService $simpleSecurity, GeolocationService $geolocationService)
    {
        $this->simpleSecurity     = $simpleSecurity;
        $this->geolocationService = $geolocationService;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            // CAPA 1: Escaneo rápido sin consultas a BD
            $threatScore = $this->quickThreatScan($request);

                                      // CAPA 2: Análisis profundo solo si es necesario
            if ($threatScore >= 40) { // Capturar eventos medios, altos y críticos
                $detailedScore = $this->detailedThreatAnalysis($request);

                // Si es crítico, bloquear y registrar outcome=blocked
                if ($detailedScore >= 80) {
                    $this->logSecurityEvent($request, $detailedScore, 'blocked', 403);
                    return $this->generateBlockResponse();
                }

                // No crítico: dejar pasar y registrar outcome según el código de respuesta
                $response = $next($request);
                $status   = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;
                $outcome  = ($status !== null && $status >= 500) ? 'exploited' : 'attempted';
                $this->logSecurityEvent($request, $detailedScore, $outcome, $status);
                return $response;
            }

            // Si score < 40, continuar normalmente sin registrar evento
            return $next($request);

        } catch (\Exception $e) {
            // En caso de error, permitir la request sin escribir a laravel.log
            return $next($request);
        }
    }

    /**
     * CAPA 1: Escaneo rápido sin consultas a BD
     */
    protected function quickThreatScan(Request $request): float
    {
        $threatScore = 0;

        // Validar y limpiar el contenido antes del procesamiento
        $content = $request->getContent();
        $allData = $request->all();

        // Asegurar codificación UTF-8 válida
        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }

        // Limpiar datos antes de json_encode
        $cleanData = [];
        foreach ($allData as $key => $value) {
            if (is_string($value) && ! mb_check_encoding($value, 'UTF-8')) {
                $cleanData[$key] = mb_convert_encoding($value, 'UTF-8', 'auto');
            } else {
                $cleanData[$key] = $value;
            }
        }

        $payload = $content . json_encode($cleanData, JSON_UNESCAPED_UNICODE);

        // Patrones críticos simples
        $criticalPatterns = [
            'sql_injection'     => ['/union\s+select/i', '/drop\s+table/i', '/--/'],
            'xss_attack'        => ['/<script/i', '/javascript:/i', '/onload=/i'],
            'path_traversal'    => ['/\.\.\//', '/\.\.\\\\/', '/%2e%2e%2f/'],
            'command_injection' => ['/;/', '/\|/', '/&&/', '/`/'],
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
        $patternScore   = $this->quickThreatScan($request);

        // Score total: máximo entre análisis de comportamiento y patrones
        $totalScore = max($threatAnalysis['total_score'], $patternScore);

        return $totalScore;
    }

    /**
     * Registrar eventos de seguridad para todos los niveles de riesgo
     */
    protected function logSecurityEvent(Request $request, float $threatScore, ?string $outcome = null, ?int $responseStatus = null): void
    {
        $action = $threatScore >= 80 ? 'block' : 'monitor';
        $ip     = $request->ip();

        // Obtener geolocalización de la IP
        $geolocation = $this->geolocationService->getGeolocation($ip);

        // Crear evento en base de datos
        SecurityEvent::create([
            'ip_address'      => $ip,
            'request_uri'     => $request->getRequestUri(),
            'request_method'  => $request->method(),
            'threat_score'    => $threatScore,
            'reason'          => $this->getThreatReason($threatScore),
            'risk_level'      => $this->categorizeRisk($threatScore),
            'category'        => $this->detectAttackCategory($request),
            'source'          => 'middleware',
            'geolocation'     => $geolocation,
            // Unificar: status sigue outcome
            'status'          => $outcome ?? ($action === 'block' ? 'blocked' : 'attempted'),
            'outcome'         => $outcome,
            'response_status' => $responseStatus,
        ]);

        // Revertido: no escribir en ThreatIntelligence desde el middleware

        // Escribir a log de seguridad específico con utf8mb4
        $this->writeToSecurityLog($request, $threatScore, $geolocation, $outcome, $responseStatus);
    }

    /**
     * Escribir a log de seguridad con formato utf8mb4
     */
    protected function writeToSecurityLog(Request $request, float $threatScore, array $geolocation, ?string $outcome = null, ?int $responseStatus = null): void
    {
        $logFile   = storage_path('logs/security.log');
        $timestamp = now()->format('Y-m-d H:i:s');
        $riskLevel = $this->categorizeRisk($threatScore);

        $logEntry = sprintf(
            "[%s] %s - IP: %s | URI: %s | Method: %s | Threat Score: %.2f | Risk: %s | Category: %s | Outcome: %s | HTTP: %s | Geo: %s, %s\n",
            $timestamp,
            strtoupper($riskLevel),
            $request->ip(),
            $request->getRequestUri(),
            $request->method(),
            $threatScore,
            $riskLevel,
            $this->detectAttackCategory($request),
            $outcome ?? 'unknown',
            $responseStatus !== null ? (string) $responseStatus : 'n/a',
            $geolocation['country'] ?? 'Unknown',
            $geolocation['city'] ?? 'Unknown'
        );

        // Asegurar que se use utf8mb4 al escribir el log
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Solo registrar eventos críticos (mantener compatibilidad)
     */
    protected function logCriticalEvent(Request $request, float $threatScore): void
    {
        $this->logSecurityEvent($request, $threatScore);
    }

    /**
     * Respuesta de bloqueo
     */
    protected function generateBlockResponse()
    {
        return response()->json([
            'error'       => 'Access denied',
            'reason'      => 'Security policy violation',
            'incident_id' => uniqid('SEC-'),
            'contact'     => config('security.contact_email', 'admin@example.com'),
        ], 403);
    }

    /**
     * Métodos auxiliares
     */
    protected function getThreatWeight(string $attackType): float
    {
        $weights = [
            'sql_injection'     => 25,
            'xss_attack'        => 20,
            'path_traversal'    => 15,
            'command_injection' => 30,
        ];

        return $weights[$attackType] ?? 10;
    }

    protected function categorizeRisk(float $score): string
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
     * Obtener razón de la amenaza basada en el score
     */
    protected function getThreatReason(float $score): string
    {
        if ($score >= 80) {
            return 'Critical threat detected - Immediate action required';
        }

        if ($score >= 60) {
            return 'High threat detected - Close monitoring required';
        }

        if ($score >= 40) {
            return 'Medium threat detected - Investigation recommended';
        }

        // Solo retornar los 3 niveles principales
        return 'Medium threat detected - Investigation recommended';
    }

    /**
     * Detectar categoría de ataque basada en el request
     */
    protected function detectAttackCategory(Request $request): string
    {
        // Usar la misma lógica de limpieza que quickThreatScan
        $content = $request->getContent();
        $allData = $request->all();

        // Asegurar codificación UTF-8 válida
        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        }

        // Limpiar datos antes de json_encode
        $cleanData = [];
        foreach ($allData as $key => $value) {
            if (is_string($value) && ! mb_check_encoding($value, 'UTF-8')) {
                $cleanData[$key] = mb_convert_encoding($value, 'UTF-8', 'auto');
            } else {
                $cleanData[$key] = $value;
            }
        }

        $payload = $content . json_encode($cleanData, JSON_UNESCAPED_UNICODE);

        if (preg_match('/union\s+select|drop\s+table|--/i', $payload)) {
            return 'sql_injection';
        }

        if (preg_match('/<script|javascript:|onload=/i', $payload)) {
            return 'xss_attack';
        }

        if (preg_match('/\.\.\/|\.\.\\\\|%2e%2e%2f/i', $payload)) {
            return 'path_traversal';
        }

        if (preg_match('/;|\||&&|`/i', $payload)) {
            return 'command_injection';
        }

        return 'suspicious_activity';
    }
}
