@props([
    'icon' => 'fas fa-info-circle',
    'title' => 'No hay datos disponibles',
    'message' => 'No se encontraron registros para mostrar',
    'icon_size' => 'fa-2x',
    'icon_color' => 'text-gray-400',
])

<div class="text-center py-4">
    <i class="{{ $icon }} {{ $icon_size }} {{ $icon_color }}"></i>
    <h6 class="mt-2 text-gray-600">{{ $title }}</h6>
    <p class="mt-1 text-gray-500">{{ $message }}</p>
</div>
