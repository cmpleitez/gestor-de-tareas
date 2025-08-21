<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SecurityEvent;
use App\Services\SimpleSecurityService;
use Carbon\Carbon;

class SecurityCleanup extends Command
{
    protected $signature = 'security:cleanup {--days=30} {--force}';
    protected $description = 'Limpieza de datos antiguos del sistema de seguridad';

    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');
        
        $this->info("🧹 INICIANDO LIMPIEZA DEL SISTEMA DE SEGURIDAD");
        $this->info("===============================================");
        
        // Limpiar eventos antiguos
        $this->cleanupOldEvents($days, $force);
        
        // Limpiar cache expirado
        $this->cleanupExpiredCache();
        
        // Mostrar estadísticas
        $this->showCleanupStats();
        
        $this->info("✅ Limpieza completada exitosamente");
    }

    /**
     * Limpiar eventos antiguos
     */
    private function cleanupOldEvents(int $days, bool $force): void
    {
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("🗑️  Limpiando eventos más antiguos de {$days} días...");
        
        $oldEventsCount = SecurityEvent::where('created_at', '<', $cutoffDate)->count();
        
        if ($oldEventsCount === 0) {
            $this->info("   No hay eventos antiguos para limpiar");
            return;
        }
        
        if (!$force) {
            if (!$this->confirm("¿Eliminar {$oldEventsCount} eventos antiguos?")) {
                $this->info("   Limpieza cancelada por el usuario");
                return;
            }
        }
        
        $deletedCount = SecurityEvent::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("   ✅ Eliminados {$deletedCount} eventos antiguos");
    }

    /**
     * Limpiar cache expirado
     */
    private function cleanupExpiredCache(): void
    {
        $this->info("🗑️  Limpiando cache expirado...");
        
        $simpleSecurity = app(SimpleSecurityService::class);
        $simpleSecurity->cleanupExpiredCache();
        
        $this->info("   ✅ Cache expirado limpiado");
    }

    /**
     * Mostrar estadísticas de limpieza
     */
    private function showCleanupStats(): void
    {
        $this->info("📊 ESTADÍSTICAS POST-LIMPIEZA");
        $this->info("===============================");
        
        $totalEvents = SecurityEvent::count();
        $recentEvents = SecurityEvent::where('created_at', '>=', Carbon::now()->subDay())->count();
        $criticalEvents = SecurityEvent::where('threat_score', '>=', 80)->count();
        
        $this->info("   Total de eventos: {$totalEvents}");
        $this->info("   Eventos (últimas 24h): {$recentEvents}");
        $this->info("   Eventos críticos: {$criticalEvents}");
        
        // Calcular tamaño aproximado de la tabla
        $tableSize = $this->estimateTableSize();
        $this->info("   Tamaño estimado de tabla: {$tableSize}");
    }

    /**
     * Estimar tamaño de la tabla
     */
    private function estimateTableSize(): string
    {
        $totalEvents = SecurityEvent::count();
        $estimatedBytes = $totalEvents * 500; // Estimación aproximada por registro
        
        if ($estimatedBytes < 1024) {
            return $estimatedBytes . ' B';
        } elseif ($estimatedBytes < 1024 * 1024) {
            return round($estimatedBytes / 1024, 2) . ' KB';
        } else {
            return round($estimatedBytes / (1024 * 1024), 2) . ' MB';
        }
    }
}
