<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecurityEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'threat_score' => $this->threat_score,
            'risk_level' => $this->risk_level,
            'category' => $this->category,
            'severity' => $this->severity,
            'status' => $this->status,
            'reason' => $this->reason,
            'source' => $this->source,
            
            // Información geográfica
            'geolocation' => [
                'country' => $this->geolocation['country'] ?? 'Unknown',
                'city' => $this->geolocation['city'] ?? 'Unknown',
                'region' => $this->geolocation['region'] ?? 'Unknown',
                'country_code' => $this->geolocation['country_code'] ?? null,
                'coordinates' => [
                    'lat' => $this->geolocation['latitude'] ?? null,
                    'lng' => $this->geolocation['longitude'] ?? null,
                ],
            ],
            
            // Información de la request
            'request' => [
                'uri' => $this->request_uri,
                'method' => $this->request_method,
                'user_agent' => $this->user_agent,
            ],
            
            // Metadatos
            'confidence' => $this->confidence,
            'session_id' => $this->session_id,
            'notes' => $this->notes,
            
            // Timestamps
            'created_at' => [
                'raw' => $this->created_at,
                'formatted' => $this->created_at->format('Y-m-d H:i:s'),
                'human' => $this->created_at->diffForHumans(),
                'age_minutes' => $this->created_at->diffInMinutes(now()),
            ],
            'updated_at' => [
                'raw' => $this->updated_at,
                'formatted' => $this->updated_at->format('Y-m-d H:i:s'),
                'human' => $this->updated_at->diffForHumans(),
            ],
            
            // Información del usuario (si existe)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'role' => $this->user->main_role,
                ];
            }),
            
            // Relaciones
            'related_events_count' => $this->when($this->relationLoaded('relatedEvents'), function () {
                return $this->relatedEvents->count();
            }),
            
            // Campos calculados
            'is_critical' => $this->threat_score >= 80,
            'is_high_risk' => $this->threat_score >= 60,
            'is_recent' => $this->created_at->diffInHours(now()) <= 24,
            'threat_level_icon' => $this->getThreatLevelIcon(),
            'risk_color' => $this->getRiskColor(),
            'severity_color' => $this->getSeverityColor(),
            
            // Enlaces de acción
            'actions' => [
                'view' => route('security.events') . '?event_id=' . $this->id,
                'investigate' => route('security.events') . '?investigate=' . $this->id,
                'block_ip' => route('security.ip-reputation') . '?block=' . $this->ip_address,
            ],
        ];
    }

    /**
     * Obtener icono del nivel de amenaza
     */
    private function getThreatLevelIcon(): string
    {
        return match ($this->threat_score) {
            $this->threat_score >= 80 => '🚨',
            $this->threat_score >= 60 => '⚠️',
            $this->threat_score >= 40 => '🔶',
            $this->threat_score >= 20 => '🔶',
            default => '✅',
        };
    }

    /**
     * Obtener color del nivel de riesgo
     */
    private function getRiskColor(): string
    {
        return match ($this->risk_level) {
            'critical' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#ffc107',
            'low' => '#20c997',
            'minimal' => '#6c757d',
            default => '#6c757d',
        };
    }

    /**
     * Obtener color de la severidad
     */
    private function getSeverityColor(): string
    {
        return match ($this->severity) {
            'critical' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#ffc107',
            'low' => '#20c997',
            'info' => '#17a2b8',
            default => '#6c757d',
        };
    }
}
