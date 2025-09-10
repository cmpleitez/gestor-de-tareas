<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreatIntelligence extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'threat_intelligence';

    /**
     * Los atributos que son asignables masivamente
     */
    protected $fillable = [
        'ip_address',
        'threat_score',
        'classification',
        'confidence',
        'data',
        'sources',
        'last_updated',
        'first_seen',
        'last_seen',
        'threat_type',
        'malware_family',
        'attack_vectors',
        'targeted_sectors',
        'geographic_origin',
        'country_code',
        'latitude',
        'longitude',
        'status',
        'verified',
        'false_positive',
        'metadata',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'threat_score'     => 'float',
        'confidence'       => 'float',
        'sources'          => 'array',
        'attack_vectors'   => 'array',
        'targeted_sectors' => 'array',
        'metadata'         => 'array',
        'last_updated'     => 'datetime',
        'first_seen'       => 'datetime',
        'last_seen'        => 'datetime',
        'verified'         => 'boolean',
        'false_positive'   => 'boolean',
        'latitude'         => 'float',
        'longitude'        => 'float',
    ];

    /**
     * Los atributos que deben ser ocultos en arrays
     */
    protected $hidden = [
        'data',
        'metadata',
    ];

    /**
     * Los atributos que deben ser agregados al array del modelo
     */
    protected $appends = [
        'formatted_classification',
        'formatted_threat_type',
        'age_in_days',
        'is_active',
        'risk_level',
    ];

    /**
     * Relación con eventos de seguridad
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'ip_address', 'ip_address');
    }

    /**
     * Scope para amenazas activas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para amenazas verificadas
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verified', true);
    }

    /**
     * Scope para amenazas de alto riesgo
     */
    public function scopeHighRisk(Builder $query): Builder
    {
        return $query->where('threat_score', '>=', 60);
    }

    /**
     * Scope para amenazas críticas
     */
    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('threat_score', '>=', 80);
    }

    /**
     * Scope para amenazas de un país específico
     */
    public function scopeFromCountry(Builder $query, string $countryCode): Builder
    {
        return $query->where('country_code', $countryCode);
    }

    /**
     * Scope para amenazas de un tipo específico
     */
    public function scopeOfType(Builder $query, string $threatType): Builder
    {
        return $query->where('threat_type', $threatType);
    }

    /**
     * Scope para amenazas de una familia de malware específica
     */
    public function scopeMalwareFamily(Builder $query, string $family): Builder
    {
        return $query->where('malware_family', $family);
    }

    /**
     * Scope para amenazas recientes (últimos 30 días)
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('last_updated', '>=', now()->subDays(30));
    }

    /**
     * Scope para amenazas de una organización específica
     */
    public function scopeFromOrganization(Builder $query, string $organization): Builder
    {
        return $query->where('organization', 'like', "%{$organization}%");
    }

    /**
     * Scope para amenazas con alta confianza
     */
    public function scopeHighConfidence(Builder $query): Builder
    {
        return $query->where('confidence', '>=', 80);
    }

    /**
     * Scope para amenazas no verificadas
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('verified', false);
    }

    /**
     * Scope para amenazas que no son falsos positivos
     */
    public function scopeNotFalsePositive(Builder $query): Builder
    {
        return $query->where('false_positive', false);
    }

    /**
     * Obtener la clasificación formateada
     */
    public function getFormattedClassificationAttribute(): string
    {
        return ucfirst($this->classification ?? 'unknown');
    }

    /**
     * Obtener el tipo de amenaza formateado
     */
    public function getFormattedThreatTypeAttribute(): string
    {
        return ucfirst($this->threat_type ?? 'unknown');
    }

    /**
     * Obtener la edad de la amenaza en días
     */
    public function getAgeInDaysAttribute(): int
    {
        if (! $this->first_seen) {
            return 0;
        }

        return $this->first_seen->diffInDays(now());
    }

    /**
     * Verificar si la amenaza está activa
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Obtener el nivel de riesgo basado en el score
     */
    public function getRiskLevelAttribute(): string
    {
        if ($this->threat_score >= 80) {
            return 'critical';
        }

        if ($this->threat_score >= 60) {
            return 'high';
        }

        if ($this->threat_score >= 40) {
            return 'medium';
        }

        if ($this->threat_score >= 20) {
            return 'low';
        }

        return 'minimal';
    }

    /**
     * Obtener la edad de la amenaza en formato legible
     */
    public function getAgeForHumansAttribute(): string
    {
        if (! $this->first_seen) {
            return 'Unknown';
        }

        return $this->first_seen->diffForHumans();
    }

    /**
     * Obtener la última actualización en formato legible
     */
    public function getLastUpdatedForHumansAttribute(): string
    {
        if (! $this->last_updated) {
            return 'Never';
        }

        return $this->last_updated->diffForHumans();
    }

    /**
     * Actualizar el score de amenaza
     */
    public function updateThreatScore(float $newScore, string $reason = null): bool
    {
        $updateData = [
            'threat_score' => $newScore,
            'last_updated' => now(),
        ];

        if ($reason) {
            $updateData['notes'] = $reason;
        }

        return $this->update($updateData);
    }

    /**
     * Agregar fuentes de inteligencia
     */
    public function addSources(array $sources): bool
    {
        $currentSources = $this->sources ?? [];
        $updatedSources = array_merge($currentSources, $sources);

        return $this->update(['sources' => $updatedSources]);
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
     * Obtener eventos de seguridad relacionados
     */
    public function getRelatedSecurityEvents(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return $this->securityEvents()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de la amenaza
     */
    public function getThreatStatistics(): array
    {
        $relatedEvents = $this->getRelatedSecurityEvents(100);

        return [
            'total_related_events' => $relatedEvents->count(),
            'average_threat_score' => $relatedEvents->avg('threat_score'),
            'highest_threat_score' => $relatedEvents->max('threat_score'),
            'most_common_category' => $relatedEvents->groupBy('category')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
            'threat_trend'         => $this->calculateThreatTrend($relatedEvents),
            'geographic_spread'    => $this->calculateGeographicSpread($relatedEvents),
        ];
    }

    /**
     * Obtener amenazas relacionadas por tipo
     */
    public function getRelatedThreatsByType(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->threat_type) {
            return collect();
        }

        return static::where('threat_type', $this->threat_type)
            ->where('id', '!=', $this->id)
            ->orderBy('threat_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener amenazas relacionadas por familia de malware
     */
    public function getRelatedThreatsByMalwareFamily(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->malware_family) {
            return collect();
        }

        return static::where('malware_family', $this->malware_family)
            ->where('id', '!=', $this->id)
            ->orderBy('threat_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calcular la tendencia de amenazas
     */
    protected function calculateThreatTrend($events): string
    {
        if ($events->count() < 2) {
            return 'stable';
        }

        $scores = $events->pluck('threat_score')->toArray();
        $trend  = $this->calculateLinearTrend($scores);

        if ($trend > 0.1) {
            return 'increasing';
        }

        if ($trend < -0.1) {
            return 'decreasing';
        }

        return 'stable';
    }

    /**
     * Calcular la dispersión geográfica
     */
    protected function calculateGeographicSpread($events): array
    {
        $countries = $events->pluck('geolocation.country_code')->filter()->unique();
        $regions   = $events->pluck('geolocation.region')->filter()->unique();
        $cities    = $events->pluck('geolocation.city')->filter()->unique();

        return [
            'countries'           => $countries->count(),
            'regions'             => $regions->count(),
            'cities'              => $cities->count(),
            'most_common_country' => $events->pluck('geolocation.country_code')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first(),
        ];
    }

    /**
     * Calcular tendencia lineal
     */
    protected function calculateLinearTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0;
        }

        $sumX  = 0;
        $sumY  = 0;
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

        // Antes de crear una amenaza
        static::creating(function ($threat) {
            if (is_null($threat->first_seen)) {
                $threat->first_seen = now();
            }

            if (is_null($threat->last_seen)) {
                $threat->last_seen = now();
            }

            if (is_null($threat->status)) {
                $threat->status = 'active';
            }

            if (is_null($threat->verified)) {
                $threat->verified = false;
            }

            if (is_null($threat->false_positive)) {
                $threat->false_positive = false;
            }
        });

        // Antes de actualizar una amenaza
        static::updating(function ($threat) {
            if ($threat->isDirty('threat_score') || $threat->isDirty('classification')) {
                $threat->last_updated = now();
            }
        });

        // Después de crear una amenaza
        static::created(function ($threat) {
            // Disparar eventos de notificación si es necesario
            if ($threat->threat_score >= 60) {
                // event(new HighThreatIntelligenceDetected($threat));
            }
        });
    }
}
