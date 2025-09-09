<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MonitorActivity extends Command
{
    protected $signature   = 'security:monitor {--log=laravel : Tipo de log a monitorear}';
    protected $description = 'Monitorear actividad de seguridad en tiempo real';

    public function handle()
    {
        $logType = $this->option('log');
        $logFile = storage_path("logs/{$logType}.log");

        if (! File::exists($logFile)) {
            $this->error("El archivo de log {$logFile} no existe.");
            return 1;
        }

        $this->info("🔍 Monitoreando actividad en {$logFile}");
        $this->info("Presiona Ctrl+C para salir");
        $this->line("");

        // Leer el archivo en tiempo real
        $handle = fopen($logFile, 'r');
        fseek($handle, 0, SEEK_END); // Ir al final del archivo

        while (true) {
            $line = fgets($handle);
            if ($line !== false) {
                // Colorear según el tipo de log
                if (strpos($line, 'SECURITY') !== false) {
                    $this->line("<fg=red>🚨 {$line}</>");
                } elseif (strpos($line, 'ERROR') !== false) {
                    $this->line("<fg=yellow>⚠️  {$line}</>");
                } elseif (strpos($line, 'INFO') !== false) {
                    $this->line("<fg=green>ℹ️  {$line}</>");
                } else {
                    $this->line($line);
                }
            }
            usleep(100000); // 0.1 segundos
        }

        fclose($handle);
        return 0;
    }
}
