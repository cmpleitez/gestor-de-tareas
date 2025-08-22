<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecurityDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'metrics' => [
                'events_24h' => [
                    'value' => $this['metrics']['events_24h'] ?? 0,
                    'label' => 'Eventos (24h)',
                    'icon' => '📊',
                    'color' => '#007bff',
                    'trend' => $this->calculateTrend('events_24h'),
                ],
                'critical_threats_24h' => [
                    'value' => $this['metrics']['critical_threats_24h'] ?? 0,
                    'label' => 'Amenazas Críticas (24h)',
                    'icon' => '🚨',
                    'color' => '#dc3545',
                    'trend' => $this->calculateTrend('critical_threats_24h'),
                ],
                'high_threats_24h' => [
                    'value' => $this['metrics']['high_threats_24h'] ?? 0,
                    'label' => 'Amenazas Altas (24h)',
                    'icon' => '⚠️',
                    'color' => '#fd7e14',
                    'trend' => $this->calculateTrend('high_threats_24h'),
                ],
                'unique_ips_24h' => [
                    'value' => $this['metrics']['unique_ips_24h'] ?? 0,
                    'label' => 'IPs Únicas (24h)',
                    'icon' => '🌐',
                    'color' => '#20c997',
                    'trend' => $this->calculateTrend('unique_ips_24h'),
                ],
                'avg_threat_score_24h' => [
                    'value' => round($this['metrics']['total_threat_score_24h'] ?? 0, 1),
                    'label' => 'Score Promedio (24h)',
                    'icon' => '🎯',
                    'color' => '#6f42c1',
                    'trend' => $this->calculateTrend('total_threat_score_24h'),
                ],
            ],

            'charts' => [
                'risk_distribution' => [
                    'type' => 'doughnut',
                    'data' => [
                        'labels' => ['Mínimo', 'Bajo', 'Medio', 'Alto', 'Crítico'],
                        'datasets' => [
                            [
                                'data' => $this['risk_distribution'] ?? [0, 0, 0, 0, 0],
                                'backgroundColor' => [
                                    '#6c757d', // minimal
                                    '#20c997', // low
                                    '#ffc107', // medium
                                    '#fd7e14', // high
                                    '#dc3545', // critical
                                ],
                                'borderWidth' => 2,
                                'borderColor' => '#ffffff',
                            ]
                        ]
                    ],
                    'options' => [
                        'responsive' => true,
                        'maintainAspectRatio' => false,
                        'plugins' => [
                            'legend' => [
                                'position' => 'bottom',
                                'labels' => [
                                    'padding' => 20,
                                    'usePointStyle' => true,
                                ]
                            ]
                        ]
                    ]
                ],

                'threats_by_country' => [
                    'type' => 'bar',
                    'data' => [
                        'labels' => array_keys($this['threats_by_country'] ?? []),
                        'datasets' => [
                            [
                                'label' => 'Cantidad de Amenazas',
                                'data' => array_column($this['threats_by_country'] ?? [], 'count'),
                                'backgroundColor' => '#007bff',
                                'borderColor' => '#0056b3',
                                'borderWidth' => 1,
                            ]
                        ]
                    ],
                    'options' => [
                        'responsive' => true,
                        'maintainAspectRatio' => false,
                        'scales' => [
                            'y' => [
                                'beginAtZero' => true,
                                'ticks' => [
                                    'stepSize' => 1
                                ]
                            ]
                        ]
                    ]
                ],

                'threat_trends' => [
                    'type' => 'line',
                    'data' => [
                        'labels' => array_column($this['threat_trends'] ?? [], 'date'),
                        'datasets' => [
                            [
                                'label' => 'Eventos',
                                'data' => array_column($this['threat_trends'] ?? [], 'events'),
                                'borderColor' => '#007bff',
                                'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                                'tension' => 0.4,
                                'fill' => true,
                            ],
                            [
                                'label' => 'Score Promedio',
                                'data' => array_column($this['threat_trends'] ?? [], 'avg_score'),
                                'borderColor' => '#fd7e14',
                                'backgroundColor' => 'rgba(253, 126, 20, 0.1)',
                                'tension' => 0.4,
                                'fill' => false,
                                'yAxisID' => 'y1',
                            ]
                        ]
                    ],
                    'options' => [
                        'responsive' => true,
                        'maintainAspectRatio' => false,
                        'scales' => [
                            'y' => [
                                'type' => 'linear',
                                'display' => true,
                                'position' => 'left',
                                'beginAtZero' => true,
                            ],
                            'y1' => [
                                'type' => 'linear',
                                'display' => true,
                                'position' => 'right',
                                'beginAtZero' => true,
                                'grid' => [
                                    'drawOnChartArea' => false,
                                ],
                            ]
                        ]
                    ]
                ]
            ],

            'recent_events' => SecurityEventResource::collection($this['recent_events'] ?? collect()),

            'top_suspicious_ips' => $this['top_suspicious_ips'] ?? collect(),

            'system_performance' => [
                'response_time' => [
                    'value' => round(($this['system_performance']['response_time_avg'] ?? 0) * 1000, 0),
                    'unit' => 'ms',
                    'label' => 'Tiempo de Respuesta Promedio',
                    'status' => $this->getPerformanceStatus('response_time_avg', 0.1),
                ],
                'threat_detection_rate' => [
                    'value' => $this['system_performance']['threat_detection_rate'] ?? 0,
                    'unit' => '%',
                    'label' => 'Tasa de Detección de Amenazas',
                    'status' => $this->getPerformanceStatus('threat_detection_rate', 90, 'higher'),
                ],
                'false_positive_rate' => [
                    'value' => $this['system_performance']['false_positive_rate'] ?? 0,
                    'unit' => '%',
                    'label' => 'Tasa de Falsos Positivos',
                    'status' => $this->getPerformanceStatus('false_positive_rate', 10, 'lower'),
                ],
                'system_uptime' => [
                    'value' => $this['system_performance']['system_uptime'] ?? 0,
                    'unit' => '%',
                    'label' => 'Uptime del Sistema',
                    'status' => $this->getPerformanceStatus('system_uptime', 99.5, 'higher'),
                ],
            ],

            'last_updated' => now()->toISOString(),
            'cache_status' => 'active',
        ];
    }

    /**
     * Calcular tendencia de una métrica
     */
    private function calculateTrend(string $metric): string
    {
        // Por ahora retornamos 'stable' - en producción se calcularía con datos históricos
        return 'stable';
    }

    /**
     * Obtener estado de rendimiento
     */
    private function getPerformanceStatus(string $metric, float $threshold, string $direction = 'lower'): string
    {
        $value = $this['system_performance'][$metric] ?? 0;
        
        if ($direction === 'higher') {
            if ($value >= $threshold) return 'excellent';
            if ($value >= $threshold * 0.9) return 'good';
            if ($value >= $threshold * 0.8) return 'warning';
            return 'critical';
        } else {
            if ($value <= $threshold) return 'excellent';
            if ($value <= $threshold * 1.1) return 'good';
            if ($value <= $threshold * 1.2) return 'warning';
            return 'critical';
        }
    }
}
