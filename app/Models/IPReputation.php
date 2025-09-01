<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class IPReputation extends Model
{
    use HasFactory;
    protected $table = 'ip_reputations';

    protected $fillable = [
        'ip_address',
        'reputation_score',
        'risk_level',
        'confidence',
        'data',
        'last_updated',
        'first_seen',
        'last_seen',
        'total_requests',
        'threat_requests',
        'benign_requests',
        'request_frequency',
        'geographic_data',
        'network_data',
        'behavioral_patterns',
        'threat_indicators',
        'whitelisted',
        'blacklisted',
        'notes',
        'metadata'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'reputation_score' => 'float',
        'confidence' => 'float',
        'data' => 'array',
        'geographic_data' => 'array',
        'network_data' => 'array',
        'behavioral_patterns' => 'array',
        'threat_indicators' => 'array',
        'metadata' => 'array',
        'last_updated' => 'datetime',
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',
        'whitelisted' => 'boolean',
        'blacklisted' => 'boolean',
        'total_requests' => 'integer',
        'threat_requests' => 'integer',
        'benign_requests' => 'integer',
        'request_frequency' => 'float'
    ];

    /**
     * Los atributos que deben ser ocultos en arrays
     */
    protected $hidden = [
        'data',
        'metadata'
    ];

    /**
     * Los atributos que deben ser agregados al array del modelo
     */
    protected $appends = [
        'formatted_risk_level',
        'threat_ratio',
        'age_in_days',
        'is_whitelisted',
        'is_blacklisted',
        'status'
    ];

    /**
     * Relación con eventos de seguridad
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'ip_address', 'ip_address');
    }

    /**
     * Relación con inteligencia de amenazas
     */
    public function threatIntelligence(): HasMany
    {
        return $this->hasMany(ThreatIntelligence::class, 'ip_address', 'ip_address');
    }

    /**
     * Scope para IPs de alto riesgo
     */
    public function scopeHighRisk(Builder $query): Builder
    {
        return $query->where('reputation_score', '>=', 60);
    }

    /**
     * Scope para IPs críticas
     */
    public function scopeCritical(Builder $query): Builder
    {
        return $query->where('reputation_score', '>=', 80);
    }

    /**
     * Scope para IPs en whitelist
     */
    public function scopeWhitelisted(Builder $query): Builder
    {
        return $query->where('whitelisted', true);
    }

    /**
     * Scope para IPs en blacklist
     */
    public function scopeBlacklisted(Builder $query): Builder
    {
        return $query->where('blacklisted', true);
    }

    /**
     * Scope para IPs con alta confianza
     */
    public function scopeHighConfidence(Builder $query): Builder
    {
        return $query->where('confidence', '>=', 80);
    }

    /**
     * Scope para IPs recientes (últimos 7 días)
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('last_updated', '>=', now()->subDays(7));
    }

    /**
     * Scope para IPs de un país específico
     */
    public function scopeFromCountry(Builder $query, string $countryCode): Builder
    {
        return $query->whereJsonContains('geographic_data->country_code', $countryCode);
    }

    /**
     * Scope para IPs de una organización específica
     */
    public function scopeFromOrganization(Builder $query, string $organization): Builder
    {
        return $query->whereJsonContains('network_data->organization', $organization);
    }

    /**
     * Scope para IPs con alta frecuencia de requests
     */
    public function scopeHighFrequency(Builder $query): Builder
    {
        return $query->where('request_frequency', '>', 100);
    }

    /**
     * Scope para IPs con patrones de comportamiento anómalos
     */
    public function scopeAnomalousBehavior(Builder $query): Builder
    {
        return $query->whereJsonLength('behavioral_patterns', '>', 5);
    }

    /**
     * Obtener el nivel de riesgo formateado
     */
    public function getFormattedRiskLevelAttribute(): string
    {
        return ucfirst($this->risk_level ?? 'unknown');
    }

    /**
     * Obtener la proporción de amenazas
     */
    public function getThreatRatioAttribute(): float
    {
        if ($this->total_requests === 0) return 0;
        return ($this->threat_requests / $this->total_requests) * 100;
    }

    /**
     * Obtener la edad de la IP en días
     */
    public function getAgeInDaysAttribute(): int
    {
        if (!$this->first_seen) return 0;
        return $this->first_seen->diffInDays(now());
    }

    /**
     * Verificar si la IP está en whitelist
     */
    public function getIsWhitelistedAttribute(): bool
    {
        return $this->whitelisted ?? false;
    }

    /**
     * Verificar si la IP está en blacklist
     */
    public function getIsBlacklistedAttribute(): bool
    {
        return $this->blacklisted ?? false;
    }

    /**
     * Obtener el estado de la IP
     */
    public function getStatusAttribute(): string
    {
        if ($this->whitelisted) return 'whitelisted';
        if ($this->blacklisted) return 'blacklisted';
        if ($this->reputation_score >= 80) return 'critical';
        if ($this->reputation_score >= 60) return 'high_risk';
        if ($this->reputation_score >= 40) return 'medium_risk';
        if ($this->reputation_score >= 20) return 'low_risk';
        return 'clean';
    }

    /**
     * Obtener la edad de la IP en formato legible
     */
    public function getAgeForHumansAttribute(): string
    {
        if (!$this->first_seen) return 'Unknown';
        return $this->first_seen->diffForHumans();
    }

    /**
     * Obtener la última actualización en formato legible
     */
    public function getLastUpdatedForHumansAttribute(): string
    {
        if (!$this->last_updated) return 'Never';
        return $this->last_updated->diffForHumans();
    }

    /**
     * Marcar la IP como whitelisted
     */
    public function markAsWhitelisted(string $reason = null, int $whitelistedByUserId = null): bool
    {
        $updateData = [
            'whitelisted' => true,
            'blacklisted' => false,
            'reputation_score' => 0,
            'risk_level' => 'minimal'
        ];

        if ($reason) {
            $updateData['notes'] = $reason;
        }

        return $this->update($updateData);
    }

    /**
     * Marcar la IP como blacklisted
     */
    public function markAsBlacklisted(string $reason = null, int $blacklistedByUserId = null): bool
    {
        $updateData = [
            'blacklisted' => true,
            'whitelisted' => false,
            'reputation_score' => 100,
            'risk_level' => 'critical'
        ];

        if ($reason) {
            $updateData['notes'] = $reason;
        }

        return $this->update($updateData);
    }

    /**
     * Actualizar el score de reputación
     */
    public function updateReputationScore(float $newScore, string $reason = null): bool
    {
        $updateData = [
            'reputation_score' => $newScore,
            'risk_level' => $this->calculateRiskLevel($newScore),
            'last_updated' => now()
        ];

        if ($reason) {
            $updateData['notes'] = $reason;
        }

        return $this->update($updateData);
    }

    /**
     * Actualizar estadísticas de requests
     */
    public function updateRequestStats(int $totalRequests, int $threatRequests): bool
    {
        $benignRequests = $totalRequests - $threatRequests;
        $requestFrequency = $this->calculateRequestFrequency($totalRequests);

        return $this->update([
            'total_requests' => $totalRequests,
            'threat_requests' => $threatRequests,
            'benign_requests' => $benignRequests,
            'request_frequency' => $requestFrequency,
            'last_updated' => now()
        ]);
    }

    /**
     * Agregar datos geográficos
     */
    public function addGeographicData(array $geographicData): bool
    {
        $currentData = $this->geographic_data ?? [];
        $updatedData = array_merge($currentData, $geographicData);

        return $this->update(['geographic_data' => $updatedData]);
    }

    /**
     * Agregar datos de red
     */
    public function addNetworkData(array $networkData): bool
    {
        $currentData = $this->network_data ?? [];
        $updatedData = array_merge($currentData, $networkData);

        return $this->update(['network_data' => $updatedData]);
    }

    /**
     * Agregar patrones de comportamiento
     */
    public function addBehavioralPatterns(array $patterns): bool
    {
        $currentPatterns = $this->behavioral_patterns ?? [];
        $updatedPatterns = array_merge($currentPatterns, $patterns);

        return $this->update(['behavioral_patterns' => $updatedPatterns]);
    }

    /**
     * Agregar indicadores de amenaza
     */
    public function addThreatIndicators(array $indicators): bool
    {
        $currentIndicators = $this->threat_indicators ?? [];
        $updatedIndicators = array_merge($currentIndicators, $indicators);

        return $this->update(['threat_indicators' => $updatedIndicators]);
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
     * Obtener inteligencia de amenazas relacionada
     */
    public function getRelatedThreatIntelligence(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->threatIntelligence()
            ->orderBy('threat_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de la IP
     */
    public function getIPStatistics(): array
    {
        $relatedEvents = $this->getRelatedSecurityEvents(100);
        $relatedThreats = $this->getRelatedThreatIntelligence(10);
        
        return [
            'total_related_events' => $relatedEvents->count(),
            'average_threat_score' => $relatedEvents->avg('threat_score'),
            'highest_threat_score' => $relatedEvents->max('threat_score'),
            'most_common_category' => $relatedEvents->groupBy('category')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
            'threat_trend' => $this->calculateThreatTrend($relatedEvents),
            'geographic_spread' => $this->calculateGeographicSpread($relatedEvents),
            'total_threat_intelligence' => $relatedThreats->count(),
            'average_threat_intelligence_score' => $relatedThreats->avg('threat_score')
        ];
    }

    /**
     * Obtener IPs relacionadas por comportamiento
     */
    public function getRelatedIPsByBehavior(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($this->behavioral_patterns)) {
            return collect();
        }

        // Buscar IPs con patrones de comportamiento similares
        return static::where('id', '!=', $this->id)
            ->whereJsonLength('behavioral_patterns', '>', 0)
            ->orderBy('reputation_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener IPs relacionadas por red
     */
    public function getRelatedIPsByNetwork(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($this->network_data)) {
            return collect();
        }

        $asn = $this->network_data['asn'] ?? null;
        $organization = $this->network_data['organization'] ?? null;

        if (!$asn && !$organization) {
            return collect();
        }

        $query = static::where('id', '!=', $this->id);

        if ($asn) {
            $query->whereJsonContains('network_data->asn', $asn);
        }

        if ($organization) {
            $query->whereJsonContains('network_data->organization', $organization);
        }

        return $query->orderBy('reputation_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calcular el nivel de riesgo basado en el score
     */
    protected function calculateRiskLevel(float $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'minimal';
    }

    /**
     * Calcular la frecuencia de requests
     */
    protected function calculateRequestFrequency(int $totalRequests): float
    {
        if ($this->first_seen) {
            $daysSinceFirstSeen = $this->first_seen->diffInDays(now());
            if ($daysSinceFirstSeen > 0) {
                return $totalRequests / $daysSinceFirstSeen;
            }
        }
        
        return $totalRequests;
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
     * Calcular la dispersión geográfica
     */
    protected function calculateGeographicSpread($events): array
    {
        $countries = $events->pluck('geolocation.country_code')->filter()->unique();
        $regions = $events->pluck('geolocation.region')->filter()->unique();
        $cities = $events->pluck('geolocation.city')->filter()->unique();

        return [
            'countries' => $countries->count(),
            'regions' => $regions->count(),
            'cities' => $cities->count(),
            'most_common_country' => $events->pluck('geolocation.country_code')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first()
        ];
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

        // Antes de crear una reputación de IP
        static::creating(function ($reputation) {
            if (is_null($reputation->first_seen)) {
                $reputation->first_seen = now();
            }

            if (is_null($reputation->last_seen)) {
                $reputation->last_seen = now();
            }

            if (is_null($reputation->last_updated)) {
                $reputation->last_updated = now();
            }

            if (is_null($reputation->whitelisted)) {
                $reputation->whitelisted = false;
            }

            if (is_null($reputation->blacklisted)) {
                $reputation->blacklisted = false;
            }

            if (is_null($reputation->total_requests)) {
                $reputation->total_requests = 0;
            }

            if (is_null($reputation->threat_requests)) {
                $reputation->threat_requests = 0;
            }

            if (is_null($reputation->benign_requests)) {
                $reputation->benign_requests = 0;
            }
        });

        // Antes de actualizar una reputación de IP
        static::updating(function ($reputation) {
            if ($reputation->isDirty('reputation_score') || $reputation->isDirty('risk_level')) {
                $reputation->last_updated = now();
            }

            if ($reputation->isDirty('total_requests') || $reputation->isDirty('threat_requests')) {
                $reputation->last_updated = now();
            }
        });

        // Después de crear una reputación de IP
        static::created(function ($reputation) {
            // Disparar eventos de notificación si es necesario
            if ($reputation->reputation_score >= 60) {
                // event(new HighRiskIPDetected($reputation));
            }
        });
    }
}
