<?php
namespace App\Console\Commands;

use App\Models\SecurityEvent;
use App\Models\ThreatIntelligence;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CorrelateThreatIntelligence extends Command
{
    protected $signature   = 'security:correlate-ti {--since=3 : Días hacia atrás} {--dry-run : No escribe en BD}';
    protected $description = 'Correlaciona security_events recientes hacia threat_intelligence de forma idempotente';

    public function handle(): int
    {
        $days   = (int) $this->option('since');
        $dryRun = (bool) $this->option('dry-run');
        $since  = Carbon::now()->subDays(max(0, $days));

        $this->info("Correlacionando eventos desde {$since->toDateTimeString()} (dry-run=" . ($dryRun ? 'yes' : 'no') . ")");

        $events = SecurityEvent::where('created_at', '>=', $since)->orderBy('created_at', 'desc')->get();
        if ($events->isEmpty()) {
            $this->warn('No hay eventos recientes para correlacionar.');
            return self::SUCCESS;
        }

        $count   = 0;
        $updated = 0;
        $created = 0;
        foreach ($events as $e) {
            $ip = $e->ip_address;
            if (! $ip) {continue;}

            $classification = strtolower((string) ($e->risk_level ?? 'unknown')); // critical|high|medium
            $score          = (float) ($e->threat_score ?? 0);
            $confidence     = max(50, min(95, (float) ($e->threat_score ?? 60)));
            $status         = in_array(strtolower((string) ($e->outcome ?? $e->status ?? '')), ['attempted']) ? 'monitoring' : 'active';

            $payload = [
                'threat_score'      => $score,
                'classification'    => $classification,
                'confidence'        => $confidence,
                'threat_type'       => $e->category,
                'geographic_origin' => trim(($e->country ?? '')) ?: null,
                'last_updated'      => Carbon::now(),
                'status'            => $status,
                'metadata'          => [
                    'source'          => 'security_events',
                    'event_id'        => $e->id,
                    'response_status' => $e->response_status ?? null,
                ],
            ];

            if ($dryRun) {
                $this->line("[dry] TI {$ip} => " . json_encode($payload));
            } else {
                $existing = ThreatIntelligence::where('ip_address', $ip)->first();
                if ($existing) {$updated++;} else { $created++;}
                ThreatIntelligence::updateOrCreate(
                    ['ip_address' => $ip],
                    $payload
                );
            }

            $count++;
        }

        if (! $dryRun) {
            $this->info("Procesados: {$count}. Creados: {$created}. Actualizados: {$updated}.");
        } else {
            $this->info("Procesados (dry-run): {$count}.");
        }

        return self::SUCCESS;
    }
}
