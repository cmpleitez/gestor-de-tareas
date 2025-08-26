@props([
    'title' => 'SEGURIDAD DEL SITIO WEB',
    'status_color' => 'danger',
    'show_pulse' => true,
])

<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-body p-0">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-11">
                        <h6 class="m-0 align-items-center" style="display: flex; align-items: center;">
                            <i class="bx bxs-check-shield me-3 text-dark" style="padding-left: 0rem !important; padding-right: 0.2rem !important; font-size: 3rem;"></i>
                            {{ $title }}
                        </h6>
                    </div>
                    <div class="col-md-1 text-center">
                        <div class="d-flex justify-content-end align-items-center">
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
