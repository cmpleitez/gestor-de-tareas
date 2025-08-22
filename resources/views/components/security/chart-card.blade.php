@props([
    'title' => '',
    'icon' => 'fas fa-chart-pie',
    'chart_id' => 'chart',
    'chart_height' => '300px',
    'show_dropdown' => false,
    'dropdown_items' => [],
])

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="{{ $icon }} me-2"></i>
            {{ $title }}
        </h6>
        @if ($show_dropdown)
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in">
                    @foreach ($dropdown_items as $item)
                        <a class="dropdown-item" href="#" onclick="{{ $item['onclick'] ?? '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="card-body">
        <div class="chart-area" style="height: {{ $chart_height }}; position: relative; width: 100%; display: block;">
            <canvas id="{{ $chart_id }}" style="max-width: 100%; max-height: {{ $chart_height }};"></canvas>
        </div>
    </div>
</div>
