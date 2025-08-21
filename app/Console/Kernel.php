<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ========================================
        // TAREAS DE LIMPIEZA AUTOMÁTICA DE SEGURIDAD
        // ========================================

        // Limpieza diaria de eventos antiguos (reduce almacenamiento)
        $schedule->command('security:cleanup --days=30 --force')
            ->daily()
            ->at('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Limpieza de cache cada 6 horas (optimiza memoria)
        $schedule->command('security:cleanup --days=1 --force')
            ->everyFourHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Monitoreo de uso de recursos (sin consultas costosas)
        $schedule->command('security:monitor stats')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
