@props([
    'title' => '',
    'chart_id' => 'chart',
    'chart_height' => '300px',
    'show_dropdown' => false,
    'dropdown_items' => [],
])

<div class="card mt-1">
    <div class="card-header">
        <span class="card-title" style="font-size: 0.875rem; font-weight: 500;">{{ $title }}</span>
    </div>
    <div class="card-body">
        <div class="chart-area" style="height: {{ $chart_height }}; position: relative; width: 100%; display: block;">
            <canvas id="{{ $chart_id }}" style="max-width: 100%; max-height: {{ $chart_height }};"></canvas>
        </div>
    </div>
</div>
