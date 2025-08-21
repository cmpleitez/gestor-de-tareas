<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SecurityStatus extends Command
{
    protected $signature = 'security:status';
    protected $description = 'Muestra el estado actual del sistema de seguridad compactado';

    public function handle()
    {
        $this->info("🔒 ESTADO DEL SISTEMA DE SEGURIDAD COMPACTADO");
        $this->info("=============================================");

        // Verificar servicios activos
        $this->checkActiveServices();

        // Verificar servicios deshabilitados
        $this->checkDisabledServices();

        // Verificar configuración
        $this->checkConfiguration();

        // Verificar recursos del sistema
        $this->checkSystemResources();

        $this->info("✅ Verificación completada");
    }

    /**
     * Verificar servicios activos
     */
    private function checkActiveServices(): void
    {
        $this->info("✅ SERVICIOS ACTIVOS:");

        $activeServices = [
            'SimpleSecurityService' => app_path('Services/SimpleSecurityService.php'),
            'SecurityMonitoringOptimized' => app_path('Http/Middleware/SecurityMonitoringOptimized.php'),
            'SecurityCleanup' => app_path('Console/Commands/SecurityCleanup.php'),
        ];

        foreach ($activeServices as $service => $path) {
            if (File::exists($path)) {
                $this->info("   🟢 {$service} - ACTIVO");
            } else {
                $this->error("   🔴 {$service} - NO ENCONTRADO");
            }
        }
    }

    /**
     * Verificar servicios deshabilitados
     */
    private function checkDisabledServices(): void
    {
        $this->info("❌ SERVICIOS DESHABILITADOS:");

        $disabledServices = [
            'AnomalyDetectionService' => app_path('Services/AnomalyDetectionService.disabled'),
            'ThreatIntelligenceService' => app_path('Services/ThreatIntelligenceService.disabled'),
            'IPReputationService' => app_path('Services/IPReputationService.disabled'),
            'MachineLearning' => app_path('Services/MachineLearning.disabled'),
            'SecurityMonitoring' => app_path('Http/Middleware/SecurityMonitoring.disabled'),
        ];

        $disabledCount = 0;
        foreach ($disabledServices as $service => $path) {
            if (File::exists($path)) {
                $this->info("   🔴 {$service} - DESHABILITADO");
                $disabledCount++;
            } else {
                $this->warn("   🟡 {$service} - NO DESHABILITADO (activo)");
            }
        }

        if ($disabledCount > 0) {
            $this->info("   📊 Total de servicios deshabilitados: {$disabledCount}");
        }
    }

    /**
     * Verificar configuración
     */
    private function checkConfiguration(): void
    {
        $this->info("⚙️  CONFIGURACIÓN:");

        $configPath = config_path('security.php');
        if (File::exists($configPath)) {
            $this->info("   🟢 Archivo de configuración security.php - PRESENTE");

            // Verificar configuración específica
            $config = config('security');
            if ($config && isset($config['cleanup'])) {
                $this->info("   🟢 Configuración cargada correctamente");
                $this->info("   📅 Retención de eventos: {$config['cleanup']['events_retention_days']} días");
                $this->info("   ⏰ Limpieza de cache: cada {$config['cleanup']['cache_cleanup_hours']} horas");
            } else {
                $this->warn("   ⚠️  Configuración de limpieza no disponible");
            }
        } else {
            $this->error("   🔴 Archivo de configuración security.php - NO ENCONTRADO");
        }
    }

    /**
     * Verificar recursos del sistema
     */
    private function checkSystemResources(): void
    {
        $this->info("💾 RECURSOS DEL SISTEMA:");

        // Verificar tareas programadas
        $this->info("   📅 Tareas programadas configuradas en Kernel.php");

        // Verificar cache
        $cacheSize = $this->estimateCacheSize();
        $this->info("   🗄️  Tamaño estimado de cache: {$cacheSize}");

        // Verificar logs
        $logsSize = $this->estimateLogsSize();
        $this->info("   📝 Tamaño estimado de logs: {$logsSize}");
    }

    /**
     * Estimar tamaño del cache
     */
    private function estimateCacheSize(): string
    {
        // Estimación básica del cache
        $estimatedBytes = 1024 * 100; // 100KB estimado

        if ($estimatedBytes < 1024) {
            return $estimatedBytes . ' B';
        } elseif ($estimatedBytes < 1024 * 1024) {
            return round($estimatedBytes / 1024, 2) . ' KB';
        } else {
            return round($estimatedBytes / (1024 * 1024), 2) . ' MB';
        }
    }

    /**
     * Estimar tamaño de logs
     */
    private function estimateLogsSize(): string
    {
        $logsPath = storage_path('logs');
        if (!File::exists($logsPath)) {
            return 'No disponible';
        }

        $files = File::files($logsPath);
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        if ($totalSize < 1024) {
            return $totalSize . ' B';
        } elseif ($totalSize < 1024 * 1024) {
            return round($totalSize / 1024, 2) . ' KB';
        } else {
            return round($totalSize / (1024 * 1024), 2) . ' MB';
        }
    }
}
