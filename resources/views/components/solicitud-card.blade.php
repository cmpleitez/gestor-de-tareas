@props([
'solicitud',
'borderColor' => '#ffc107',
'estadoColor' => 'rgb(170, 95, 34)',
'badgeColor' => 'badge-secondary',
'roleColor' => '#612d03'
])

<div class="solicitud-card"
    data-id="{{ $solicitud['recepcion_id'] }}"
    data-atencion-id="{{ $solicitud['atencion_id'] }}"
    data-estado-id="{{ $solicitud['estado_id'] }}"
    data-fecha="{{ $solicitud['created_at'] }}"
    style="border-left-color: {{ $borderColor }};">

    <div class="solicitud-titulo">
        @if ($solicitud['titulo'] && $solicitud['detalle'])
        {{ $solicitud['titulo'] }} - {{ $solicitud['detalle'] }}
        @elseif($solicitud['titulo'])
        {{ $solicitud['titulo'] }}
        @elseif($solicitud['detalle'])
        {{ $solicitud['detalle'] }}
        @else
        Sin título
        @endif
    </div>

    <div class="row">
        <div class="solicitud-id text-center">
            <small style="font-size: 0.7rem;">
                {{ $solicitud['solicitud_id_ripped'] }}
            </small>
        </div>
        <div class="fecha-solicitud">
            {{ $solicitud['fecha_relativa'] }}
        </div>
    </div>

    <div class="solicitud-estado" style="font-size: 11px; color: {{ $estadoColor }} !important; margin-top: 5px;">
        Estado: {{ $solicitud['estado'] }}
    </div>

    <div class="progress-divider"
        data-atencion-id="{{ $solicitud['atencion_id'] }}"
        data-avance="{{ $solicitud['porcentaje_progreso'] }}">
    </div>
    
    <div class="users-container" style="display: flex; align-items: center; justify-content: end; margin-top: 8px; padding-top: 6px;">
        @foreach ($solicitud['users'] as $user)
            <div style="margin: 0;" data-toggle="popover" 
                data-title="{{ $user['name'] }}" 
                data-content="<span class='badge badge-pill {{ $badgeColor }}'>{{ $user['recepcion_role_name'] }}</span> 
                <span class='badge badge-pill {{ $badgeColor }}'>{{ $user['area_name'] }}</span>"
                data-trigger="hover"
                data-placement="top">
                @if ($user['profile_photo_url'])
                    <img src="{{ $user['profile_photo_url'] }}" alt="Usuario" class="avatar"
                        style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                @else
                    <div class="avatar"
                        style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">
                        {{ $user['name'][0] }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>


</div>