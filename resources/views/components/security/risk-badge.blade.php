@props([
    'threat_score' => 0,
    'show_score' => true,
    'size' => 'normal',
])

@php
    $riskLevel = match (true) {
        $threat_score >= 80 => 'critical',
        $threat_score >= 60 => 'high',
        $threat_score >= 40 => 'medium',
        $threat_score >= 20 => 'low',
        default => 'minimal',
    };

    $riskLabel = match ($riskLevel) {
        'critical' => 'Crítico',
        'high' => 'Alto',
        'medium' => 'Medio',
        'low' => 'Bajo',
        'minimal' => 'Mínimo',
        default => 'Desconocido',
    };

    $badgeClass = match ($riskLevel) {
        'critical' => 'danger',
        'high' => 'warning',
        'medium' => 'warning',
        'low' => 'success',
        'minimal' => 'secondary',
        default => 'secondary',
    };

    $sizeClass = match ($size) {
        'small' => 'badge-sm',
        'large' => 'badge-lg',
        default => '',
    };
@endphp

<span class="badge bg-{{ $badgeClass }} {{ $sizeClass }} risk-badge">
    @if ($show_score)
        {{ $threat_score }} -
    @endif
    {{ $riskLabel }}
</span>
