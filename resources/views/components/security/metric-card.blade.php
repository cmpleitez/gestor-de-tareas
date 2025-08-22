@props([
    'title' => '',
    'value' => 0,
    'icon' => 'fas fa-chart-line',
    'color' => 'primary',
    'trend' => null,
    'trend_direction' => 'up',
    'subtitle' => null,
])

<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-{{ $color }} shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                        {{ $title }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $value }}
                        @if ($trend)
                            <span class="text-{{ $trend_direction === 'up' ? 'success' : 'danger' }} fs-6 ms-2">
                                <i class="fas fa-arrow-{{ $trend_direction }}"></i>
                                {{ $trend }}
                            </span>
                        @endif
                    </div>
                    @if ($subtitle)
                        <small class="text-muted">{{ $subtitle }}</small>
                    @endif
                </div>
                <div class="col-auto">
                    <i class="{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
