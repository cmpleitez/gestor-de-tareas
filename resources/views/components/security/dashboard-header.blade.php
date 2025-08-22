@props([
    'title' => 'Dashboard de Seguridad',
    'subtitle' => 'Sistema avanzado de monitoreo con Machine Learning y análisis de amenazas en tiempo real',
    'status' => 'OPERATIVO',
    'status_color' => 'success',
    'show_pulse' => true,
])

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">
                            <i class="fas fa-shield-alt me-3"></i>
                            {{ $title }}
                        </h1>
                        <p class="mb-0 fs-5">
                            {{ $subtitle }}
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="me-4">
                                <div class="fs-6 opacity-75">Estado del Sistema</div>
                                <div class="fs-4 fw-bold">
                                    <span class="badge bg-{{ $status_color }} fs-6">{{ $status }}</span>
                                </div>
                            </div>
                            @if ($show_pulse)
                                <div class="security-status-indicator">
                                    <div class="pulse-dot bg-{{ $status_color }}"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
