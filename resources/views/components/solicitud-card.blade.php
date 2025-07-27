@props([
    'solicitud',
    'borderColor' => '#ffc107',
    'estadoColor' => 'rgb(170, 95, 34)',
    'badgeColor' => 'badge-light-warning',
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
    
    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
        <div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
            <div style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                {{ $solicitud['user_name'] }}
            </div>
            <div style="text-align: right; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: 500; display: inline-block; margin-left: auto;">
                <span style="color: {{ $estadoColor }} !important;" class="badge badge-pill {{ $badgeColor }}">{{ $solicitud['role_name'] }}</span>
                <span style="color: {{ $roleColor }} !important; font-weight: 400;">del área {{ $solicitud['area'] }}</span> 
            </div>
        </div>

        <div style="margin-left: 8px;">
            @if ($solicitud['user_foto'])
                <img src="{{ $solicitud['user_foto'] }}" alt="Usuario" class="avatar"
                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
            @else
                <div class="avatar"
                     style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">
                    ?
                </div>
            @endif
        </div>
    </div>
</div> 