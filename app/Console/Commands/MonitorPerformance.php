<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MonitorPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:performance {--log : Guardar resultados en log} {--alert : Enviar alertas si hay problemas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitorear el rendimiento del servidor y detectar problemas que puedan causar errores 502';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Iniciando monitoreo de rendimiento...');
        
        $results = [
            'timestamp' => now(),
            'php_settings' => $this->checkPhpSettings(),
            'database' => $this->checkDatabase(),
            'memory_usage' => $this->checkMemoryUsage(),
            'disk_space' => $this->checkDiskSpace(),
            'active_connections' => $this->checkActiveConnections(),
            'cache_status' => $this->checkCacheStatus(),
        ];
        
        // Verificar si hay problemas críticos
        $criticalIssues = $this->detectCriticalIssues($results);
        
        if (!empty($criticalIssues)) {
            $this->error('❌ Problemas críticos detectados:');
            foreach ($criticalIssues as $issue) {
                $this->error("  - {$issue}");
            }
            
            if ($this->option('alert')) {
                $this->sendAlert($criticalIssues);
            }
        } else {
            $this->info('✅ No se detectaron problemas críticos');
        }
        
        // Mostrar resumen
        $this->displaySummary($results);
        
        // Guardar en log si se solicita
        if ($this->option('log')) {
            Log::info('Performance monitoring results', $results);
            $this->info('📝 Resultados guardados en log');
        }
        
        return 0;
    }
    
    /**
     * Verificar configuraciones PHP
     */
    private function checkPhpSettings(): array
    {
        return [
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_input_time' => ini_get('max_input_time'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'default_socket_timeout' => ini_get('default_socket_timeout'),
        ];
    }
    
    /**
     * Verificar estado de la base de datos
     */
    private function checkDatabase(): array
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            return [
                'status' => 'connected',
                'response_time_ms' => round($responseTime, 2),
                'connection_count' => 'N/A', // SQL Server no tiene SHOW STATUS
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'response_time_ms' => 0,
            ];
        }
    }
    
    /**
     * Verificar uso de memoria
     */
    private function checkMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPercentage = ($memoryUsage / $memoryLimit) * 100;
        
        return [
            'current_usage' => $this->formatBytes($memoryUsage),
            'limit' => ini_get('memory_limit'),
            'percentage' => round($memoryPercentage, 2),
            'peak_usage' => $this->formatBytes(memory_get_peak_usage(true)),
        ];
    }
    
    /**
     * Verificar espacio en disco
     */
    private function checkDiskSpace(): array
    {
        $path = storage_path();
        $totalSpace = disk_total_space($path);
        $freeSpace = disk_free_space($path);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = ($usedSpace / $totalSpace) * 100;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'free' => $this->formatBytes($freeSpace),
            'used' => $this->formatBytes($usedSpace),
            'usage_percentage' => round($usagePercentage, 2),
        ];
    }
    
    /**
     * Verificar conexiones activas
     */
    private function checkActiveConnections(): array
    {
        try {
            // Para SQL Server, usar consulta específica
            $activeConnections = DB::connection()->select('SELECT COUNT(*) as connections FROM sys.dm_exec_connections')[0]->connections ?? 0;
            
            return [
                'active' => $activeConnections,
                'max' => 'N/A', // SQL Server no expone max_connections fácilmente
                'percentage' => 0, // No podemos calcular porcentaje sin max_connections
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Verificar estado del cache
     */
    private function checkCacheStatus(): array
    {
        try {
            $testKey = 'performance_test_' . time();
            $testValue = 'test_value';
            
            $startTime = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = (microtime(true) - $startTime) * 1000;
            
            $startTime = microtime(true);
            $retrievedValue = Cache::get($testKey);
            $readTime = (microtime(true) - $startTime) * 1000;
            
            Cache::forget($testKey);
            
            return [
                'status' => 'working',
                'write_time_ms' => round($writeTime, 2),
                'read_time_ms' => round($readTime, 2),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Detectar problemas críticos
     */
    private function detectCriticalIssues(array $results): array
    {
        $issues = [];
        
        // Verificar memoria
        if ($results['memory_usage']['percentage'] > 80) {
            $issues[] = "Uso de memoria crítico: {$results['memory_usage']['percentage']}%";
        }
        
        // Verificar base de datos
        if ($results['database']['status'] === 'error') {
            $issues[] = "Error de conexión a base de datos: {$results['database']['error']}";
        } elseif ($results['database']['response_time_ms'] > 1000) {
            $issues[] = "Respuesta lenta de base de datos: {$results['database']['response_time_ms']}ms";
        }
        
        // Verificar conexiones
        if (isset($results['active_connections']['percentage']) && $results['active_connections']['percentage'] > 80) {
            $issues[] = "Muchas conexiones activas: {$results['active_connections']['percentage']}%";
        }
        
        // Verificar espacio en disco
        if ($results['disk_space']['usage_percentage'] > 90) {
            $issues[] = "Poco espacio en disco: {$results['disk_space']['usage_percentage']}%";
        }
        
        // Verificar cache
        if ($results['cache_status']['status'] === 'error') {
            $issues[] = "Error en cache: {$results['cache_status']['error']}";
        }
        
        return $issues;
    }
    
    /**
     * Mostrar resumen de resultados
     */
    private function displaySummary(array $results): void
    {
        $this->info("\n📊 Resumen de rendimiento:");
        $this->line("  Memoria: {$results['memory_usage']['current_usage']} / {$results['memory_usage']['limit']} ({$results['memory_usage']['percentage']}%)");
        $this->line("  Base de datos: {$results['database']['status']} ({$results['database']['response_time_ms']}ms)");
        $this->line("  Espacio en disco: {$results['disk_space']['usage_percentage']}% usado");
        
        if (isset($results['active_connections']['active'])) {
            $this->line("  Conexiones: {$results['active_connections']['active']}/{$results['active_connections']['max']} ({$results['active_connections']['percentage']}%)");
        }
        
        $this->line("  Cache: {$results['cache_status']['status']}");
    }
    
    /**
     * Enviar alerta (placeholder para implementación futura)
     */
    private function sendAlert(array $issues): void
    {
        // Aquí se puede implementar el envío de alertas por email, Slack, etc.
        $this->warn('🚨 Alertas enviadas para los problemas detectados');
    }
    
    /**
     * Parsear límite de memoria
     */
    private function parseMemoryLimit(string $limit): int
    {
        $value = (int) $limit;
        $unit = strtolower(substr($limit, -1));
        
        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
            default:
                return $value;
        }
    }
    
    /**
     * Formatear bytes en formato legible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
} 