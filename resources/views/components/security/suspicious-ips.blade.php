@props([
    'ips' => collect(),
    'title' => 'Top 10 IPs Sospechosas',
    'icon' => 'fas fa-list-ol',
])

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="{{ $icon }} me-2"></i>
            {{ $title }}
        </h6>
    </div>
    <div class="card-body">
        @if ($ips->count() > 0)
            @foreach ($ips as $ip)
                <div class="suspicious-ip-item">
                    <div>
                        <strong>{{ $ip->ip_address }}</strong>
                        <br><small class="text-muted">Eventos: {{ $ip->event_count }}</small>
                        @if (isset($ip->country) && $ip->country !== 'Unknown')
                            <br><small class="text-muted">
                                <i class="fas fa-globe me-1"></i>{{ $ip->country }}
                            </small>
                        @endif
                    </div>
                    <div class="text-end">
                        <div
                            class="text-{{ $ip->avg_threat_score >= 80 ? 'danger' : ($ip->avg_threat_score >= 60 ? 'warning' : 'success') }} fw-bold">
                            Score: {{ round($ip->avg_threat_score, 1) }}
                        </div>
                        <span
                            class="badge bg-{{ $ip->avg_threat_score >= 80 ? 'danger' : ($ip->avg_threat_score >= 60 ? 'warning' : 'success') }} risk-badge">
                            {{ $ip->avg_threat_score >= 80 ? 'Crítico' : ($ip->avg_threat_score >= 60 ? 'Alto' : 'Bajo') }}
                        </span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                <p class="mt-2 text-gray-500">No hay IPs sospechosas</p>
            </div>
        @endif
    </div>
</div>
