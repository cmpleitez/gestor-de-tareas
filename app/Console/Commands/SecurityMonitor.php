<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence;
use App\Models\IPReputation;
use Carbon\Carbon;

class SecurityMonitor extends Command
{
    protected $signature = 'security:monitor {action} {--ip=} {--days=7} {--risk-level=}';
    protected $description = 'Comando para gestionar el sistema de monitoreo de seguridad';

    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'status':
                $this->showSecurityStatus();
                break;
            case 'analyze':
                $this->analyzeSecurityData();
                break;
            case 'cleanup':
                $this->cleanupOldData();
                break;
            case 'stats':
                $this->showSecurityStats();
                break;
            case 'block':
                $this->blockIP();
                break;
            case 'whitelist':
                $this->whitelistIP();
                break;
            default:
                $this->error('Acción no válida. Use: status, analyze, cleanup, stats, block, whitelist');
                return 1;
        }

        return 0;
    }

    private function showSecurityStatus()
    {
        $this->info('🔒 ESTADO DEL SISTEMA DE SEGURIDAD');
        $this->info('=====================================');

        // Eventos recientes
        $recentEvents = SecurityEvent::where('created_at', '>=', Carbon::now()->subDays(1))->count();
        $this->info("📊 Eventos de seguridad (últimas 24h): {$recentEvents}");

        // Amenazas activas
        $activeThreats = ThreatIntelligence::where('status', 'active')->count();
        $this->info("⚠️  Amenazas activas: {$activeThreats}");

        // IPs bloqueadas
        $blockedIPs = IPReputation::where('blacklisted', true)->count();
        $this->info("🚫 IPs bloqueadas: {$blockedIPs}");

        // IPs en whitelist
        $whitelistedIPs = IPReputation::where('whitelisted', true)->count();
        $this->info("✅ IPs en whitelist: {$whitelistedIPs}");

        // Eventos críticos
        $criticalEvents = SecurityEvent::where('risk_level', 'critical')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        $this->info("🚨 Eventos críticos (última semana): {$criticalEvents}");

        $this->newLine();
    }

    private function analyzeSecurityData()
    {
        $this->info('🔍 ANALIZANDO DATOS DE SEGURIDAD');
        $this->info('==================================');

        $days = $this->option('days');
        $startDate = Carbon::now()->subDays($days);

        // Análisis de eventos por nivel de riesgo
        $riskLevels = SecurityEvent::where('created_at', '>=', $startDate)
            ->selectRaw('risk_level, COUNT(*) as count')
            ->groupBy('risk_level')
            ->orderBy('count', 'desc')
            ->get();

        $this->info("📈 Distribución de eventos por nivel de riesgo (últimos {$days} días):");
        foreach ($riskLevels as $level) {
            $icon = $this->getRiskIcon($level->risk_level);
            $this->line("  {$icon} {$level->risk_level}: {$level->count} eventos");
        }

        // Top IPs más activas
        $topIPs = SecurityEvent::where('created_at', '>=', $startDate)
            ->selectRaw('ip_address, COUNT(*) as count, AVG(threat_score) as avg_score')
            ->groupBy('ip_address')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $this->newLine();
        $this->info("🌐 Top 10 IPs más activas:");
        foreach ($topIPs as $index => $ip) {
            $rank = $index + 1;
            $this->line("  {$rank}. {$ip->ip_address} - {$ip->count} eventos (Score: " . round($ip->avg_score, 1) . ")");
        }

        // Análisis geográfico
        $this->newLine();
        $this->info("🌍 Análisis geográfico:");
        $countries = SecurityEvent::where('created_at', '>=', $startDate)
            ->whereNotNull('geolocation->country_code')
            ->selectRaw('JSON_EXTRACT(geolocation, "$.country_code") as country, COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        foreach ($countries as $country) {
            $this->line("  🏳️  {$country->country}: {$country->count} eventos");
        }

        $this->newLine();
    }

    private function cleanupOldData()
    {
        $this->info('🧹 LIMPIEZA DE DATOS ANTIGUOS');
        $this->info('===============================');

        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        // Limpiar eventos antiguos
        $deletedEvents = SecurityEvent::where('created_at', '<', $cutoffDate)->delete();
        $this->info("🗑️  Eventos eliminados: {$deletedEvents}");

        // Limpiar amenazas inactivas
        $deletedThreats = ThreatIntelligence::where('status', 'inactive')
            ->where('updated_at', '<', $cutoffDate)
            ->delete();
        $this->info("🗑️  Amenazas inactivas eliminadas: {$deletedThreats}");

        // Limpiar reputaciones antiguas
        $deletedReputations = IPReputation::where('updated_at', '<', $cutoffDate)
            ->where('reputation_score', '<', 30)
            ->delete();
        $this->info("🗑️  Reputaciones antiguas eliminadas: {$deletedReputations}");

        $this->info("✅ Limpieza completada. Datos anteriores a {$days} días eliminados.");
        $this->newLine();
    }

    private function showSecurityStats()
    {
        $this->info('📊 ESTADÍSTICAS DE SEGURIDAD');
        $this->info('==============================');

        $totalEvents = SecurityEvent::count();
        $totalThreats = ThreatIntelligence::count();
        $totalReputations = IPReputation::count();

        $this->info("📈 Total de eventos: {$totalEvents}");
        $this->info("⚠️  Total de amenazas: {$totalThreats}");
        $this->info("🌐 Total de reputaciones: {$totalReputations}");

        // Estadísticas por mes
        $this->newLine();
        $this->info("📅 Eventos por mes (últimos 6 meses):");
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $monthlyEvents = SecurityEvent::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $monthlyThreats = ThreatIntelligence::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            
            $monthName = $month->format('M Y');
            $this->line("  📅 {$monthName}: {$monthlyEvents} eventos, {$monthlyThreats} amenazas");
        }

        // Distribución de scores
        $this->newLine();
        $this->info("🎯 Distribución de threat scores:");
        
        $scoreRanges = [
            '0-20' => [0, 20],
            '21-40' => [21, 40],
            '41-60' => [41, 60],
            '61-80' => [61, 80],
            '81-100' => [81, 100]
        ];

        foreach ($scoreRanges as $range => $limits) {
            $count = SecurityEvent::whereBetween('threat_score', $limits)->count();
            $percentage = $totalEvents > 0 ? round(($count / $totalEvents) * 100, 1) : 0;
            $this->line("  📊 {$range}: {$count} eventos ({$percentage}%)");
        }

        $this->newLine();
    }

    private function blockIP()
    {
        $ip = $this->option('ip');
        if (!$ip) {
            $this->error('❌ Debe especificar una IP con --ip=');
            return;
        }

        $this->info("🚫 BLOQUEANDO IP: {$ip}");
        
        // Actualizar reputación
        $reputation = IPReputation::where('ip_address', $ip)->first();
        if ($reputation) {
            $reputation->update([
                'blacklisted' => true,
                'reputation_score' => 100,
                'risk_level' => 'critical',
                'notes' => 'IP bloqueada manualmente por administrador'
            ]);
            $this->info("✅ Reputación actualizada");
        } else {
            // Crear nueva reputación
            IPReputation::create([
                'ip_address' => $ip,
                'reputation_score' => 100,
                'risk_level' => 'critical',
                'blacklisted' => true,
                'notes' => 'IP bloqueada manualmente por administrador'
            ]);
            $this->info("✅ Nueva reputación creada");
        }

        // Crear evento de seguridad
        SecurityEvent::create([
            'ip_address' => $ip,
            'threat_score' => 100,
            'reason' => 'IP bloqueada manualmente por administrador',
            'risk_level' => 'critical',
            'severity' => 'critical',
            'category' => 'manual_block',
            'source' => 'admin_command'
        ]);

        $this->info("✅ IP {$ip} bloqueada exitosamente");
        $this->newLine();
    }

    private function whitelistIP()
    {
        $ip = $this->option('ip');
        if (!$ip) {
            $this->error('❌ Debe especificar una IP con --ip=');
            return;
        }

        $this->info("✅ AGREGANDO IP A WHITELIST: {$ip}");
        
        // Actualizar reputación
        $reputation = IPReputation::where('ip_address', $ip)->first();
        if ($reputation) {
            $reputation->update([
                'whitelisted' => true,
                'blacklisted' => false,
                'reputation_score' => 0,
                'risk_level' => 'minimal',
                'notes' => 'IP agregada a whitelist por administrador'
            ]);
            $this->info("✅ Reputación actualizada");
        } else {
            // Crear nueva reputación
            IPReputation::create([
                'ip_address' => $ip,
                'reputation_score' => 0,
                'risk_level' => 'minimal',
                'whitelisted' => true,
                'notes' => 'IP agregada a whitelist por administrador'
            ]);
            $this->info("✅ Nueva reputación creada");
        }

        // Crear evento de seguridad
        SecurityEvent::create([
            'ip_address' => $ip,
            'threat_score' => 0,
            'reason' => 'IP agregada a whitelist por administrador',
            'risk_level' => 'minimal',
            'severity' => 'info',
            'category' => 'manual_whitelist',
            'source' => 'admin_command'
        ]);

        $this->info("✅ IP {$ip} agregada a whitelist exitosamente");
        $this->newLine();
    }

    private function getRiskIcon(string $riskLevel): string
    {
        return match ($riskLevel) {
            'critical' => '🚨',
            'high' => '⚠️',
            'medium' => '🔶',
            'low' => '🔶',
            'minimal' => '✅',
            default => '❓'
        };
    }
}
