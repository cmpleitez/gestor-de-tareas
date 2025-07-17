@extends('dashboard')

@section('css')
    <!-- SweetAlert2 CSS Local -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <!-- Bootstrap Extended CSS para compatibilidad con SweetAlert2 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-kanban.css') }}">
    <style>
        .solicitud-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: move;
            transition: all 0.2s;
            border-left: 4px solid #007bff;
        }

        .solicitud-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .solicitud-titulo {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .solicitud-id {
            font-size: 11px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }

        /* Estilos para drag & drop */
        .kanban-columna {
            min-height: 400px;
            padding: 10px;
        }

        /* Hacer que todas las columnas tengan la misma altura */
        .row {
            display: flex !important;
            align-items: stretch !important;
        }

        .col-md-4 {
            display: flex !important;
        }

        .col-md-4 .card {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .card-body.kanban-columna {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .sortable-column {
            min-height: 380px;
            /* Área de drop fija y grande */
            border: 2px dashed transparent;
            border-radius: 8px;
            transition: all 0.3s ease;
            flex: 1;
            /* Ocupa todo el espacio disponible */
            display: flex;
            flex-direction: column;
        }

        .sortable-column:empty {
            border-color: #e9ecef;
            /* Borde visible cuando está vacía */
            background: #f8f9fa;
        }

        /* Estilos mejorados para las tarjetas */
        .solicitud-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: move;
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Línea de progreso parametrizada */
        .progress-divider {
            height: 3px;
            width: 100%;
            border-radius: 1.5px;
            margin: 8px 0 6px 0;
            background: linear-gradient(to right, 
                #ff8c00 0%, #ff8c00 var(--naranja, 15%), 
                #ffd700 var(--naranja, 15%), #ffd700 calc(var(--naranja, 15%) + var(--amarillo, 25%)), 
                #28a745 calc(var(--naranja, 15%) + var(--amarillo, 25%)), #28a745 calc(var(--naranja, 15%) + var(--amarillo, 25%) + var(--verde, 35%)), 
                #17a2b8 calc(var(--naranja, 15%) + var(--amarillo, 25%) + var(--verde, 35%)), #17a2b8 100%
            );
            transition: all 0.3s ease;
        }

        .solicitud-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .solicitud-titulo {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: #2c3e50;
        }

        .solicitud-id {
            font-size: 11px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 500;
        }

        /* Mejoras en los encabezados de las columnas */
        .card {
            border-radius: 8px !important;
            overflow: hidden;
        }

        .card-header {
            border: none !important;
            margin: 0 !important;
            padding: 0.6rem !important;
        }

        .badge {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            background-color: rgba(255, 255, 255, 0.9) !important;
            color: #333 !important;
        }

        .sortable-chosen {
            /* MOSTRAR SOLO LA TARJETA CHOSEN */
            opacity: 1 !important;
            background: white !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6) !important;
            transform: rotate(5deg) scale(1.15) !important;
            z-index: 99999 !important;
            border: 4px solid #007bff !important;
            border-radius: 10px !important;
        }

        .sortable-ghost {
            /* OCULTAR COMPLETAMENTE LA TARJETA GHOST */
            opacity: 0 !important;
            visibility: hidden !important;
            display: none !important;
        }

        /* FORZAR que cualquier solicitud siendo arrastrada sea visible */
        .solicitud-card {
            opacity: 1 !important;
        }

        /* Accordion elegante */
        #heading5 {
            background: linear-gradient(156deg, #221627 0%, #4e2a5d 100%) !important;
            min-height: 60px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
        }

        #heading5[aria-expanded="true"] .accordion-arrow {
            transform: rotate(180deg);
        }

        /* Cards con altura uniforme */
        .accordion .row {
            display: flex;
            align-items: stretch;
        }

        .accordion .col-md-3 {
            display: flex;
        }

        .accordion .card {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .accordion .card-header {
            flex-shrink: 0;
        }

        .accordion .card-body {
            flex: 1;
        }

        /* Diseño de selección de items */
        .item-selector {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            padding: 20px;
            width: 100%;
            height: 100%;
        }

        .item-selector:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .item-selector.selected {
            border-color: #007bff;
            box-shadow: 0 4px 16px rgba(0, 123, 255, 0.2);
        }

        /* Triángulo de color en esquina superior derecha */
        .item-selector::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 20px 20px 0;
            border-color: transparent #221627 transparent transparent;
            border-radius: 0 8px 0 0;
        }

        .item-selector .item-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .item-selector .item-info {
            flex: 1;
        }

        .item-selector .item-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .item-selector .item-desc {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .item-selector .radio-indicator {
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
        }

        .item-selector.selected .radio-indicator {
            border-color: #007bff;
            background: #007bff;
        }

        .item-selector.selected .radio-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* Estilos para checkbox de tareas del sidebar */
        .checkbox-indicator {
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
            top: 16px;
            right: 14px;
        }

        .checkbox-indicator.checked {
            border-color: #007bff;
            background: #007bff;
        }

        .checkbox-indicator.checked::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        /* Forzar sin sombra en el acordeón */
        .collapse-header,
        .collapse-header *,
        .accordion,
        .accordion *,
        #accordionWrapa2,
        #accordionWrapa2 * {
            box-shadow: none !important;
            filter: none !important;
        }
    </style>
@endsection

@section('contenedor')
    {{-- ITEMS DESTINATARIOS PARA CADA ROL --}}
    <div class="row">
        <div class="col-12">
            <div class="accordion" id="accordionWrapa2">
                <div class="card collapse-header border-0 overflow-hidden">
                    <div id="heading5" class="card-header" data-toggle="collapse" data-target="#accordion5"
                        aria-expanded="false" aria-controls="accordion5" role="tablist"
                        style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; margin: 0; cursor: pointer; transition: all 0.3s ease;">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center" style="padding: 0.75rem;">
                                    <i class="bx bx-target-lock text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div class="d-flex flex-column align-items-start justify-content-center">
                                    <h6 class="mb-0 text-white font-weight-500"
                                        style="font-size: 1rem; letter-spacing: 0.3px;">
                                        @if (auth()->user()->hasRole('Recepcionista'))
                                            <span class="font-weight-600">{{ auth()->user()->area->area }}</span>
                                        @elseif(auth()->user()->hasRole('Supervisor'))
                                            <span
                                                class="font-weight-600">{{ auth()->user()->equipos()->first()->equipo }}</span>
                                        @elseif(auth()->user()->hasRole('Gestor'))
                                            <span class="font-weight-600">{{ auth()->user()->name }}</span>
                                        @endif
                                    </h6>
                                    <small class="text-white-50" style="font-size: 0.8rem;">
                                        Selecciona el destino para derivar solicitudes
                                    </small>
                                </div>
                            </div>
                            <div>
                                <i class="bx bx-chevron-down text-white accordion-arrow"
                                    style="font-size: 1rem; transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                    </div>
                    <div id="accordion5" role="tabpanel" data-parent="#accordionWrapa2" aria-labelledby="heading5"
                        class="collapse">
                        <div class="card-content">
                            <div class="card-body" style="background: #f8f9fa; padding: 1rem;">
                                @if (auth()->user()->hasRole('Recepcionista') && isset($areas))
                                    {{-- RECEPCIONISTA --}}
                                    <div class="row" style="display: flex; align-items: stretch;">
                                        @foreach ($areas as $area)
                                            <div class="col-md-3">
                                                <div class="item-selector {{ $area->id == auth()->user()->area_id ? 'selected' : '' }}"
                                                    onclick="selectItem('area_{{ $area->id }}')">
                                                    <div class="item-body">
                                                        <div class="item-info">
                                                            <div class="item-name">{{ $area->area }}</div>
                                                            <div class="item-desc">Área de trabajo</div>
                                                        </div>
                                                        <div class="radio-indicator"></div>
                                                    </div>
                                                    <input type="radio" id="area_{{ $area->id }}" name="area_destino"
                                                        value="{{ $area->id }}"
                                                        {{ $area->id == auth()->user()->area_id ? 'checked' : '' }}
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif (auth()->user()->hasRole('Supervisor') && isset($equipos))
                                    {{-- SUPERVISOR --}}
                                    <div class="row" style="display: flex; align-items: stretch;">
                                        @foreach ($equipos as $equipo)
                                            <div class="col-md-3">
                                                <div class="item-selector {{ $equipo->id == auth()->user()->equipos->first()->id ? 'selected' : '' }}"
                                                    onclick="selectItem('equipo_{{ $equipo->id }}')">
                                                    <div class="item-body">
                                                        <div class="item-info">
                                                            <div class="item-name">{{ $equipo->equipo }}</div>
                                                            <div class="item-desc">Grupo de trabajo</div>
                                                        </div>
                                                        <div class="radio-indicator"></div>
                                                    </div>
                                                    <input type="radio" id="equipo_{{ $equipo->id }}"
                                                        name="equipo_destino" value="{{ $equipo->id }}"
                                                        {{ $equipo->id == auth()->user()->equipos->first()->id ? 'checked' : '' }}
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif (auth()->user()->hasRole('Gestor') && isset($operadores))
                                    {{-- GESTOR --}}
                                    <div class="row" style="display: flex; align-items: stretch;">
                                        @foreach ($operadores as $operador)
                                            <div class="col-md-3">
                                                <div class="item-selector {{ $operador->id == auth()->user()->id ? 'selected' : '' }}"
                                                    onclick="selectItem('operador_{{ $operador->id }}')">
                                                    <div class="item-body">
                                                        <div class="item-info">
                                                            <div class="item-name">{{ $operador->name }}</div>
                                                            <div class="item-desc">Personal asignado</div>
                                                        </div>
                                                        <div class="radio-indicator"></div>
                                                    </div>
                                                    <input type="radio" id="operador_{{ $operador->id }}"
                                                        name="operador_destino" value="{{ $operador->id }}"
                                                        {{ $operador->id == auth()->user()->id ? 'checked' : '' }}
                                                        style="display: none;">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="bx bx-info-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">No hay elementos disponibles para tu rol.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- TABLEROS KANBAN --}}
    @php
        $recibidas = $tarjetas->where('estado_id', 1);
        $progreso = $tarjetas->where('estado_id', 2);
        $resueltas = $tarjetas->where('estado_id', 3);
    @endphp
    <div class="row" style="display: flex; align-items: stretch;">
        <div class="col-md-4">
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-archive text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Recibidas</h6>
                        <span class="badge badge-light ml-auto" id="contador-recibidas">{{ $recibidas->count() }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-recibidas" class="sortable-column">
                        @forelse($recibidas as $recepcion)
                            <div class="solicitud-card" data-id="{{ $recepcion['recepcion_id'] }}"
                                data-estado-id="{{ $recepcion['estado_id'] }}" style="border-left-color: #ffc107;">
                                <div class="solicitud-titulo">
                                    {{ $recepcion['titulo'] ?? ($recepcion['detalle'] ?? 'Sin título') }}</div>
                                <div class="solicitud-id">ID: {{ $recepcion['atencion_id'] }}</div>
                                <div class="solicitud-estado" style="font-size: 11px; color: #ffc107; margin-top: 5px;">
                                    Estado: {{ $recepcion['estado'] }} ({{ $recepcion['recepcion_id'] }})
                                </div>
                                <div class="progress-divider" data-recepcion-id="{{ $recepcion['recepcion_id'] }}"></div>
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
                                    <div
                                        style="display: flex; flex-direction: column; justify-content: center; height: 32px; flex: 1;">
                                        <div
                                            style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                            {{ $recepcion['user_name'] }}
                                        </div>
                                        <div
                                            style="text-align: right; background:rgb(239, 242, 247); padding: 1px 6px; border-radius: 3px; font-size: 9px; color: #495057; font-weight: 500; display: inline-block; margin-left: auto;">
                                            {{ $recepcion['role_name'] . ' del área ' . $recepcion['area'] }}
                                        </div>
                                    </div>
                                    <div style="margin-left: 8px;">
                                        @if ($recepcion['user_foto'])
                                            <img src="{{ $recepcion['user_foto'] }}" alt="Usuario" class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                                        @else
                                            <div class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">
                                                ?</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-archive text-muted" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">Sin solicitudes recibidas</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-time-five text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">En Progreso</h6>
                        <span class="badge badge-light ml-auto" id="contador-progreso">{{ $progreso->count() }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-progreso" class="sortable-column">
                        @forelse($progreso as $recepcion)
                            <div class="solicitud-card" data-id="{{ $recepcion['recepcion_id'] }}"
                                data-estado-id="{{ $recepcion['estado_id'] }}" style="border-left-color: #17a2b8;">
                                <div class="solicitud-titulo">
                                    {{ $recepcion['titulo'] ?? ($recepcion['detalle'] ?? 'Sin título') }}</div>
                                <div class="solicitud-id">ID: {{ $recepcion['atencion_id'] }}</div>
                                <div class="solicitud-estado" style="font-size: 11px; color: #17a2b8; margin-top: 5px;">
                                    Estado: {{ $recepcion['estado'] }} ({{ $recepcion['recepcion_id'] }})
                                </div>
                                <div class="progress-divider" data-recepcion-id="{{ $recepcion['recepcion_id'] }}"></div>
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
                                    <div
                                        style="display: flex; flex-direction: column; justify-content: center; height: 32px; flex: 1;">
                                        <div
                                            style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                            {{ $recepcion['user_name'] }}
                                        </div>
                                        <div
                                            style="text-align: right; background:rgb(239, 242, 247); padding: 1px 6px; border-radius: 3px; font-size: 9px; color: #495057; font-weight: 500; display: inline-block; margin-left: auto;">
                                            {{ $recepcion['role_name'] . ' del área ' . $recepcion['area'] }}
                                        </div>
                                    </div>
                                    <div style="margin-left: 8px;">
                                        @if ($recepcion['user_foto'])
                                            <img src="{{ $recepcion['user_foto'] }}" alt="Usuario" class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                                        @else
                                            <div class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">
                                                ?</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-time-five text-muted" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">Sin tareas en progreso</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-check-circle text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Resueltas</h6>
                        <span class="badge badge-light ml-auto" id="contador-resueltas">{{ $resueltas->count() }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-resueltas" class="sortable-column">
                        @forelse($resueltas as $recepcion)
                            <div class="solicitud-card" data-id="{{ $recepcion['recepcion_id'] }}"
                                data-estado-id="{{ $recepcion['estado_id'] }}" style="border-left-color: #28a745;">
                                <div class="solicitud-titulo">
                                    {{ $recepcion['titulo'] ?? ($recepcion['detalle'] ?? 'Sin título') }}</div>
                                <div class="solicitud-id">ID: {{ $recepcion['atencion_id'] }}</div>
                                <div class="solicitud-estado" style="font-size: 11px; color: #28a745; margin-top: 5px;">
                                    Estado: {{ $recepcion['estado'] }} ({{ $recepcion['recepcion_id'] }})
                                </div>
                                <div class="progress-divider" data-recepcion-id="{{ $recepcion['recepcion_id'] }}"></div>
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
                                    <div
                                        style="display: flex; flex-direction: column; justify-content: center; height: 32px; flex: 1;">
                                        <div
                                            style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                            {{ $recepcion['user_name'] }}
                                        </div>
                                        <div
                                            style="text-align: right; background:rgb(239, 242, 247); padding: 1px 6px; border-radius: 3px; font-size: 9px; color: #495057; font-weight: 500; display: inline-block; margin-left: auto;">
                                            {{ $recepcion['role_name'] . ' del área ' . $recepcion['area'] }}
                                        </div>
                                    </div>
                                    <div style="margin-left: 8px;">
                                        @if ($recepcion['user_foto'])
                                            <img src="{{ $recepcion['user_foto'] }}" alt="Usuario" class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                                        @else
                                            <div class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">
                                                ?</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-check-circle text-muted" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">Sin tareas completadas</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- OVERLAY Y SIDEBAR KANVAN --}}
    <div class="kanban-overlay"></div>
    <div class="kanban-sidebar">
        <div class="d-flex justify-content-between align-items-center border-bottom px-1"
            style="background: linear-gradient(156deg, #221627 0%, #4e2a5d 100%); border: none; margin: 0; padding: 0.75rem 1rem; min-height: 52px;">
            <h4 id="sidebar-card-title" class="text-white mb-0">Titulo</h4>
            <button type="button" class="close close-icon">
                <i class="bx bx-x text-white"></i>
            </button>
        </div>
        <div id="sidebar-card-body" style="padding: 1rem;">
            <p>Selecciona una tarjeta para ver detalles...</p>
        </div>
    </div>
@endsection

@section('js')
    {{-- LOGICA KANBAN --}}
    <script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/jkanban/Sortable.min.js') }}"></script>
    <script>
        let userRole = '';
        @if (auth()->user()->hasRole('Recepcionista'))
            userRole = 'Recepcionista';
        @elseif (auth()->user()->hasRole('Supervisor'))
            userRole = 'Supervisor';
        @elseif (auth()->user()->hasRole('Gestor'))
            userRole = 'Gestor';
        @elseif (auth()->user()->hasRole('Operador'))
            userRole = 'Operador';
        @endif
        function selectItem(radioId) { // Función para seleccionar items
            document.querySelectorAll('.item-selector').forEach(selector => { // Desmarcar todos los selectores
                selector.classList.remove('selected');
            });
            const selectedElement = document.querySelector(
            `[onclick="selectItem('${radioId}')"]`); // Marcar el seleccionado
            if (selectedElement) {
                selectedElement.classList.add('selected');
            }
            const radio = document.getElementById(radioId); // Marcar el radio button
            if (radio) {
                radio.checked = true;
                $(radio).trigger('change'); // Disparar el evento change para actualizar el título
            }
        }
        // Aquí puedes mantener o adaptar la lógica de drag & drop si es necesaria, pero elimina la carga AJAX de tarjetas
        function initKanban() { // Inicializar el kanban
            const columnas = ['columna-recibidas', 'columna-progreso', 'columna-resueltas'];
            columnas.forEach(function(columnaId) {
                const elemento = document.getElementById(columnaId);
                if (!elemento) return;
                new Sortable(elemento, {
                    group: 'kanban', // Permite mover entre columnas
                    animation: 50, // Velocidad de la animación
                    ghostClass: 'sortable-ghost', // Elemento en posición original
                    chosenClass: 'sortable-chosen', // Elemento seleccionado  
                    dragClass: 'sortable-drag', // Elemento siendo arrastrado
                    onMove: function(evt) { //columna de destino con colores más tenues
                        document.querySelectorAll('.sortable-column').forEach(col => {
                            col.style.borderColor = 'transparent';
                        });
                        evt.to.style.borderColor = '#d1ecf1'; // Azul muy tenue
                        evt.to.style.backgroundColor =
                            '#f8fdff'; // Fondo casi imperceptible
                    },
                    onEnd: function(evt) { //Quitar resaltado de todas las columnas
                        document.querySelectorAll('.sortable-column').forEach(col => {
                            col.style.borderColor = col.children.length === 0 ?
                                '#e9ecef' : 'transparent';
                            col.style.backgroundColor = col.children.length === 0 ?
                                '#f8f9fa' : 'transparent';
                        });
                        const solicitudId = evt.item.dataset.id;
                        const columnaOrigen = evt.from.id;
                        const columnaDestino = evt.to.id;
                        if (columnaOrigen !== columnaDestino) {
                            showMoveAlert(solicitudId, columnaDestino, evt);
                        }
                    }
                });
            });
        }
        //ACTUALIZACION DE ESTADO EN EL FRONTEND
        function showMoveAlert(solicitudId, nuevaColumna, evt) {
            let nuevoEstadoId = 1; // Por defecto Recibida
            let nombreEstado = 'Recibida';
            let colorBorde = '#ffc107'; // Amarillo por defecto
            switch (nuevaColumna) {
                case 'columna-recibidas':
                    nuevoEstadoId = 1; // ID de Recibida
                    nombreEstado = 'Recibida';
                    colorBorde = '#ffc107'; // Amarillo
                    break;
                case 'columna-progreso':
                    nuevoEstadoId = 2; // ID de En progreso
                    nombreEstado = 'En progreso';
                    colorBorde = '#17a2b8'; // Azul info
                    break;
                case 'columna-resueltas':
                    nuevoEstadoId = 3; // ID de Resuelta
                    nombreEstado = 'Resuelta';
                    colorBorde = '#28a745'; // Verde
                    break;
            }
            let url = ''; //Seleccionando la ruta a la que se va a enviar la solicitud
            let selectedValue = null;
            if (userRole === 'Recepcionista') { //RECEPCIONISTA
                selectedValue = $('input[name="area_destino"]:checked').val();
                if (!selectedValue) {
                    Swal.fire({
                        position: 'top-end',
                        type: 'warning',
                        title: 'Selecciona un área destino primero',
                        showConfirmButton: false,
                        timer: 2000,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false
                    });
                    $(evt.from).append(evt.item);
                    return;
                }
                url = '{{ route('recepcion.derivar', ['recepcion_id' => ':id', 'area_id' => ':area']) }}'
                    .replace(':id', solicitudId)
                    .replace(':area', selectedValue);
            } else if (userRole === 'Supervisor') { //SUPERVISOR
                selectedValue = $('input[name="equipo_destino"]:checked').val();
                if (!selectedValue) {
                    Swal.fire({
                        position: 'top-end',
                        type: 'warning',
                        title: 'Selecciona un equipo destino primero',
                        showConfirmButton: false,
                        timer: 2000,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false
                    });
                    $(evt.from).append(evt.item);
                    return;
                }
                url = '{{ route('recepcion.asignar', ['recepcion_id' => ':id', 'equipo_id' => ':equipo']) }}'
                    .replace(':id', solicitudId)
                    .replace(':equipo', selectedValue);
            } else if (userRole === 'Gestor') { //GESTOR
                selectedValue = $('input[name="operador_destino"]:checked').val();
                if (!selectedValue) {
                    Swal.fire({
                        position: 'top-end',
                        type: 'warning',
                        title: 'Selecciona un operador destino primero',
                        showConfirmButton: false,
                        timer: 2000,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false
                    });
                    $(evt.from).append(evt.item);
                    return;
                }
                url = '{{ route('recepcion.delegar', ['recepcion_id' => ':id', 'user_id' => ':user']) }}'
                    .replace(':id', solicitudId)
                    .replace(':user', selectedValue);
            } else if (userRole === 'Operador') { //OPERADOR
                alert('entro');


                url = '{{ route('recepcion.iniciar-tareas', ['recepcion_id' => ':id']) }}'
                    .replace(':id', solicitudId);
            }
            //ACTUALIZAR ESTADO EN EL BACKEND
            $.ajax({
                url: url,
                method: 'POST',
                cache: false,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const tarjeta = $(`#${nuevaColumna} .solicitud-card[data-id="${solicitudId}"]`);
                        const tituloTarjeta = tarjeta.find('.solicitud-titulo').text() || 'Sin título';

                        if (tarjeta.length > 0) {
                            tarjeta.css('border-left-color', colorBorde);
                            tarjeta.find('.solicitud-estado').text('Estado: ' + nombreEstado);
                            tarjeta.find('.solicitud-estado').css('color', colorBorde);
                            tarjeta.attr('data-estado-id', nuevoEstadoId);
                        }
                        Swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: 'Solicitud #' + String(solicitudId).slice(-3) + ' "' + tituloTarjeta + '" 👉🏻 ' + nombreEstado,
                            showConfirmButton: false,
                            timer: 3000,
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: response.message,
                            showConfirmButton: true,
                            timer: 6000,
                            confirmButtonClass: 'btn btn-danger',
                            buttonsStyling: false
                        });
                        $(evt.from).append(evt.item); // Revertir la tarjeta a su posición original
                    }
                },
                error: function(xhr, status, error) {
                    let mensaje = '🚨 Error desconocido';
                    if (xhr.status === 419) {
                        mensaje = '🚨 Error CSRF - Recarga la página';
                    } else if (xhr.status === 404) {
                        mensaje = '🚨 Ruta no encontrada';
                    } else if (xhr.status === 500) {
                        mensaje = '🚨 Error del servidor';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = '🚨 ' + xhr.responseJSON.message;
                    }
                    Swal.fire({
                        position: 'top-end',
                        type: 'error',
                        title: mensaje,
                        showConfirmButton: true,
                        timer: 6000,
                        confirmButtonClass: 'btn btn-danger',
                        buttonsStyling: false
                    });
                    $(evt.from).append(evt.item); // Revertir la tarjeta a su posición original
                }
            });
        }
        
        //MOSTRAR TAREAS EN SIDEBAR
        $(document).on('click', '.solicitud-card', function() {
            const $card = $(this);
            const titulo = $card.find('.solicitud-titulo').text().trim();
            const atencion = $card.find('.solicitud-id').text().trim();
            const recepcionId = $card.data('id');
            $('#sidebar-card-title').text(titulo); // Rellenar la información en el sidebar
            $('#sidebar-card-body').html('<p>' + atencion + '</p>');
            cargarTareas(recepcionId); // Cargar y dibujar las tareas
            $('.kanban-overlay').addClass('show'); // Mostrar overlay y sidebar
            $('.kanban-sidebar').addClass('show');
        });
        function cargarTareas(recepcionId) { // Función para cargar y dibujar las tareas
            $.ajax({
                url: '{{ route('recepcion.tareas', ['recepcion_id' => ':id']) }}'.replace(':id', recepcionId),
                type: 'GET',
                cache: true,
                success: function(response) {
                    const tareas = response.tareas || [];
                    dibujarTareas(tareas);
                },
                error: function(xhr, status, error) {
                    $('#sidebar-card-body').append(
                        '<div class="text-center text-muted py-3"><i class="bx bx-error-circle text-danger"></i><div class="mt-2">Error al cargar tareas</div></div>'
                        );
                }
            });
        }
        function dibujarTareas(tareas) { // Función para dibujar las tareas en el sidebar
            if (tareas.length === 0) {
                $('#sidebar-card-body').append(
                    '<div class="text-center text-muted py-3"><i class="bx bx-task text-muted"></i><div class="mt-2">Sin tareas asignadas</div></div>'
                    );
                return;
            }
            let tareasHtml = '<div><h6 class="font-weight-600 mb-2"></h6>';
            tareas.forEach(function(tarea) {
                let estadoColor = '#6c757d'; // Gris por defecto
                let estadoIcon = 'bx-circle';

                if (tarea.estado_id === 2) { // En progreso
                    estadoColor = '#17a2b8';
                    estadoIcon = 'bx-time-five';
                } else if (tarea.estado_id === 3) { // Completada
                    estadoColor = '#28a745';
                    estadoIcon = 'bx-check-circle';
                }
                tareasHtml += `
                    <div class="item-selector ${tarea.estado_id === 3 ? 'selected' : ''}" onclick="selectTask('task_${tarea.actividad_id}')" style="margin-bottom: 12px;">
                        <div class="checkbox-indicator ${tarea.estado_id === 3 ? 'checked' : ''}" id="checkbox_${tarea.actividad_id}" style="position: absolute; z-index: 10;"></div>

                        <div class="item-body">
                            <div class="item-info">
                                <div class="item-name">${tarea.tarea}</div>
                                <div class="item-desc text-right">ID: ${tarea.actividad_id}</div>
                            </div>
                        </div>
                        <input type="checkbox" id="task_${tarea.actividad_id}" name="tarea_completada" 
                            value="${tarea.actividad_id}" ${tarea.estado_id === 3 ? 'checked' : ''} style="display: none;">
                    </div>
                `;
            });
            tareasHtml += '</div>';
            $('#sidebar-card-body').append(tareasHtml);
        }
        //ACTUALIZAR TAREAS
        function selectTask(taskId) { // Función para seleccionar tareas
            const checkbox = document.getElementById(taskId); // Marcar/desmarcar el checkbox
            const visualCheckbox = document.querySelector(`[onclick="selectTask('${taskId}')"] .checkbox-indicator`);
            const itemSelector = document.querySelector(`[onclick="selectTask('${taskId}')"]`);
            if (checkbox && visualCheckbox && itemSelector) {
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    visualCheckbox.classList.add('checked');
                    itemSelector.classList.add('selected');
                } else {
                    visualCheckbox.classList.remove('checked');
                    itemSelector.classList.remove('selected');
                }
                const actividadId = taskId.replace('task_', ''); // AJAX para actualizar estado
                const nuevoEstado = checkbox.checked ? 'Resuelta' : 'En progreso';
                $.ajax({
                    url: '{{ route('recepcion.reportar-tarea', [':id']) }}'.replace(':id', actividadId),
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        estado: nuevoEstado
                    },
                    success: function(response) {
                        if (response.success) {
                            // Actualizar progreso si se proporciona en la respuesta
                            if (response.progreso && response.recepcion_id) {
                                updateProgressByPercentage(response.recepcion_id, response.progreso.porcentaje);
                            }
                            
                            Swal.fire({
                                position: 'top-end',
                                type: 'success',
                                title: 'Tarea ' + String(actividadId).slice(-4) + ' se reportó como ' + nuevoEstado,
                                showConfirmButton: false,
                                timer: 1500,
                                confirmButtonClass: 'btn btn-primary',
                                buttonsStyling: false
                            });
                        } else {
                            Swal.fire({
                                position: 'top-end',
                                type: 'error',
                                title: response.message,
                                showConfirmButton: true,
                                timer: 6000,
                                confirmButtonClass: 'btn btn-danger',
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let mensaje = 'Error desconocido';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            mensaje = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: mensaje,
                            showConfirmButton: true,
                            timer: 6000,
                            confirmButtonClass: 'btn btn-danger',
                            buttonsStyling: false
                        });
                    }
                });
            }
        }
        $(document).on('click', '.kanban-overlay, .kanban-sidebar .close-icon',
        function() { //Cerrar sidebar al hacer clic en overlay o en el icono de cierre
            $('.kanban-overlay').removeClass('show');
            $('.kanban-sidebar').removeClass('show');
        });
        $(document).on('change', 'input[name="area_destino"]', function() { //Actualizacion dinamica del acordion
            const areaId = $(this).val();
            const areaNombre = $(this).closest('.item-selector').find('.item-name').text().trim();
            $('#heading5 h6 .font-weight-600').text(areaNombre);
            $('#accordion5').collapse('hide');
        });
        $(document).on('change', 'input[name="equipo_destino"]', function() {
            const equipoId = $(this).val();
            const equipoNombre = $(this).closest('.item-selector').find('.item-name').text().trim();
            $('#heading5 h6 .font-weight-600').text(equipoNombre);
            $('#accordion5').collapse('hide');
        });
        $(document).on('change', 'input[name="operador_destino"]', function() {
            const operadorId = $(this).val();
            const operadorNombre = $(this).closest('.item-selector').find('.item-name').text().trim();
            $('#heading5 h6 .font-weight-600').text(operadorNombre);
            $('#accordion5').collapse('hide');
        });

        // Función para actualizar progreso basado en porcentaje de tareas resueltas
        function updateProgressByPercentage(recepcionId, porcentaje) {
            let naranja, amarillo, verde, celeste;
            
            alert(porcentaje);

            // Aplicar distribución de colores según tabla proporcionada
            if (porcentaje == 0) {
                naranja = 45; amarillo = 25; verde = 20; celeste = 10;
            } else if (porcentaje <= 10) {
                naranja = 15; amarillo = 35; verde = 25; celeste = 25;
            } else if (porcentaje <= 30) {
                naranja = 0; amarillo = 50; verde = 30; celeste = 30;
            } else if (porcentaje <= 60) {
                naranja = 0; amarillo = 0; verde = 45; celeste = 55;
            } else if (porcentaje <= 80) {
                naranja = 0; amarillo = 0; verde = 50; celeste = 50;
            } else if (porcentaje <= 85) {
                naranja = 0; amarillo = 0; verde = 25; celeste = 75;
            } else { // 100%
                naranja = 0; amarillo = 0; verde = 0; celeste = 100;
            }
            
            const progressBar = $(`[data-recepcion-id="${recepcionId}"]`);
            if (progressBar.length > 0) {
                progressBar.css({
                    '--naranja': naranja + '%',
                    '--amarillo': amarillo + '%',
                    '--verde': verde + '%'
                });
            }
        }
        
        // Función para inicializar progreso de todas las tarjetas
        function initializeProgress() {
            @foreach($tarjetas as $tarjeta)
                updateProgressByPercentage('{{ $tarjeta["recepcion_id"] }}', {{ $tarjeta["porcentaje_progreso"] }});
            @endforeach
        }
        
        // Inicializar al cargar la página
        $(document).ready(function() {
            initializeProgress();
        });
    </script>
@endsection
