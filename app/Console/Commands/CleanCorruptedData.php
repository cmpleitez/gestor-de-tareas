<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanCorruptedData extends Command
{
    protected $signature = 'clean:corrupted-data {--force : Forzar limpieza sin confirmación}';
    protected $description = 'Limpia datos corruptos de la base de datos';

    public function handle()
    {
        $this->info("🧹 LIMPIEZA DE DATOS CORRUPTOS");
        $this->info("================================");

        if (!$this->option('force') && !$this->confirm('¿Estás seguro de que quieres limpiar los datos corruptos?')) {
            $this->warn('Operación cancelada.');
            return;
        }

        // Limpiar tabla security_events
        $this->cleanSecurityEvents();

        // Limpiar otros datos corruptos si existen
        $this->cleanOtherCorruptedData();

        $this->info("✅ Limpieza completada exitosamente");
    }

    private function cleanSecurityEvents(): void
    {
        if (!Schema::hasTable('security_events')) {
            $this->warn('   ⚠️  Tabla security_events no existe');
            return;
        }

        $this->info("   🗑️  Limpiando tabla security_events...");

        // Contar registros antes de limpiar
        $countBefore = DB::table('security_events')->count();
        $this->info("      📊 Registros antes: {$countBefore}");

        // Eliminar todos los registros
        DB::table('security_events')->truncate();

        $countAfter = DB::table('security_events')->count();
        $this->info("      📊 Registros después: {$countAfter}");

        $this->info("      ✅ Tabla security_events limpiada");
    }

    private function cleanOtherCorruptedData(): void
    {
        $this->info("   🧽 Limpiando otros datos corruptos...");

        // Limpiar logs de seguridad si existen
        $logFiles = [
            storage_path('logs/security.log'),
            storage_path('logs/firewall.log'),
            storage_path('logs/ids.log'),
        ];

        foreach ($logFiles as $logFile) {
            if (file_exists($logFile)) {
                unlink($logFile);
                $this->info("      🗑️  Eliminado: " . basename($logFile));
            }
        }

        $this->info("      ✅ Limpieza de archivos completada");
    }
}
