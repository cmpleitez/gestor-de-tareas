<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $table = 'security_events';
    protected $fillable = [
        'ip_address',
        'user_id',
        'session_id',
        'request_uri',
        'request_method',
        'user_agent',
        'threat_score',
        'reason',
        'payload',
        'headers',
        'geolocation',
        'risk_level',
        'confidence',
        'source',
        'category',
        'severity',
        'status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'threat_score' => 'float',
        'confidence' => 'float',
        'payload' => 'array',
        'headers' => 'array',
        'geolocation' => 'array',
        'metadata' => 'array',

        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'payload',
        'headers',
        'metadata'
    ];

    /**
     * Los atributos que deben ser agregados al array del modelo
     */
    protected $appends = [
        'formatted_risk_level',
        'formatted_severity',

        'age_in_minutes'
    ];

    /**
     * Relación con el usuario (si está autenticado)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Relación con eventos relacionados (misma IP)
     */
    public function relatedEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'ip_address', 'ip_address')
            ->where('id', '!=', $this->id);
    }

    /**
     * Relación con eventos de la misma sesión
     */
    public function sessionEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'session_id', 'session_id')
            ->where('id', '!=', $this->id);
    }

    /**
     * Scope para eventos de alto riesgo
     */
    public function scopeHighRisk(Builder $query): Builder
    {
        return $query->where('threat_score', '>=', 60);
    }

    /**
     * Scope para eventos críticos
     */
    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('threat_score', '>=', 80);
    }



    /**
     * Scope para eventos de una IP específica
     */
    public function scopeFromIP(Builder $query, string $ip): Builder
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope para eventos de un usuario específico
     */
    public function scopeFromUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para eventos de una categoría específica
     */
    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para eventos de una severidad específica
     */
    public function scopeWithSeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope para eventos recientes (últimas 24 horas)
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Scope para eventos de una ventana de tiempo específica
     */
    public function scopeInTimeWindow(Builder $query, $startTime, $endTime): Builder
    {
        return $query->whereBetween('created_at', [$startTime, $endTime]);
    }

    /**
     * Scope para eventos con score de amenaza en un rango
     */
    public function scopeWithThreatScoreRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('threat_score', [$min, $max]);
    }

    /**
     * Scope para eventos de una fuente específica
     */
    public function scopeFromSource(Builder $query, string $source): Builder
    {
        return $query->where('source', $source);
    }



    /**
     * Obtener el nivel de riesgo formateado
     */
    public function getFormattedRiskLevelAttribute(): string
    {
        return ucfirst($this->risk_level ?? 'unknown');
    }

    /**
     * Obtener la severidad formateada
     */
    public function getFormattedSeverityAttribute(): string
    {
        return ucfirst($this->severity ?? 'unknown');
    }



    /**
     * Obtener la edad del evento en minutos
     */
    public function getAgeInMinutesAttribute(): int
    {
        return $this->created_at->diffInMinutes(now());
    }

    /**
     * Obtener la edad del evento en formato legible
     */
    public function getAgeForHumansAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }





    /**
     * Actualizar el score de amenaza
     */
    public function updateThreatScore(float $newScore, string $reason = null): bool
    {
        $updateData = [
            'threat_score' => $newScore,
            'risk_level' => $this->calculateRiskLevel($newScore)
        ];

        if ($reason) {
            $updateData['reason'] = $reason;
        }

        return $this->update($updateData);
    }

    /**
     * Agregar metadatos adicionales
     */
    public function addMetadata(array $metadata): bool
    {
        $currentMetadata = $this->metadata ?? [];
        $updatedMetadata = array_merge($currentMetadata, $metadata);

        return $this->update(['metadata' => $updatedMetadata]);
    }

    /**
     * Obtener eventos relacionados por IP
     */
    public function getRelatedEventsByIP(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->relatedEvents()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener eventos relacionados por sesión
     */
    public function getRelatedEventsBySession(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->sessionEvents()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas del evento
     */
    public function getEventStatistics(): array
    {
        $relatedEvents = $this->getRelatedEventsByIP(100);
        
        return [
            'total_related_events' => $relatedEvents->count(),
            'average_threat_score' => $relatedEvents->avg('threat_score'),
            'highest_threat_score' => $relatedEvents->max('threat_score'),
            'most_common_category' => $relatedEvents->groupBy('category')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
            'threat_trend' => $this->calculateThreatTrend($relatedEvents)
        ];
    }

    /**
     * Calcular el nivel de riesgo basado en el score
     */
    protected function calculateRiskLevel(float $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        // Solo retornar los 3 niveles principales
        return 'medium';
    }

    /**
     * Calcular la tendencia de amenazas
     */
    protected function calculateThreatTrend($events): string
    {
        if ($events->count() < 2) return 'stable';

        $scores = $events->pluck('threat_score')->toArray();
        $trend = $this->calculateLinearTrend($scores);

        if ($trend > 0.1) return 'increasing';
        if ($trend < -0.1) return 'decreasing';
        return 'stable';
    }

    /**
     * Calcular tendencia lineal
     */
    protected function calculateLinearTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) return 0;

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $values[$i];
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        return $slope;
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de crear un evento
        static::creating(function ($event) {
            if (is_null($event->risk_level)) {
                $event->risk_level = $event->calculateRiskLevel($event->threat_score ?? 0);
            }

            if (is_null($event->severity)) {
                $event->severity = $event->calculateSeverity($event->threat_score ?? 0);
            }

            if (is_null($event->status)) {
                $event->status = 'open';
            }
        });

        // Después de crear un evento
        static::created(function ($event) {
            // Disparar eventos de notificación si es necesario
            if ($event->threat_score >= 60) {
                // Aquí se pueden disparar notificaciones
                // event(new HighThreatEventDetected($event));
            }
        });
    }

    /**
     * Calcular la severidad basada en el score
     */
    protected function calculateSeverity(float $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        // Solo retornar los 3 niveles principales
        return 'medium';
    }
}
