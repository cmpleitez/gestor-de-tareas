@props([
    'events' => collect(),
    'title' => 'Eventos Recientes',
    'icon' => 'fas fa-clock',
    'max_height' => '400px',
])

<div class="card h-100">
    <div class="card-header">
        <span class="card-title" style="font-size: 0.875rem; font-weight: 500;">{{ $title }}
            ({{ $events->count() }} eventos)</span>
    </div>
    <div class="card-body">
        <div class="recent-events-container" style="flex: 1; overflow-y: auto;">
            @if ($events->count() > 0)
                @foreach ($events as $event)
                    <div
                        class="recent-event-item {{ $event->threat_score >= 80 ? 'critical' : ($event->threat_score >= 60 ? 'high' : ($event->threat_score >= 40 ? 'medium' : 'low')) }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>IP {{ $event->ip_address }}</strong> -
                                {{ $event->category ?? 'Sin categoría' }}
                                <br><small class="text-muted">
                                    {{ $event->formatted_created_at ?? $event->created_at->diffForHumans() }}
                                </small>
                                @if (isset($event->country) && $event->country !== 'Unknown')
                                    <br><small class="text-muted">
                                        <i class="fas fa-globe me-1"></i>{{ $event->country }}
                                    </small>
                                @endif
                            </div>
                            <span
                                class="badge bg-{{ $event->threat_score >= 80 ? 'danger' : ($event->threat_score >= 60 ? 'warning' : ($event->threat_score >= 40 ? 'warning' : 'success')) }} risk-badge">
                                {{ $event->threat_score >= 80
                                    ? 'Crítico'
                                    : ($event->threat_score >= 60
                                        ? 'Alto'
                                        : ($event->threat_score >= 40
                                            ? 'Medio'
                                            : 'Bajo')) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                    <p class="mt-2 text-gray-500">No hay eventos recientes</p>
                </div>
            @endif
        </div>
    </div>
</div>
