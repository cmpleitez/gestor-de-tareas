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
            color:rgb(255, 255, 255);
            background:rgb(175, 178, 180);
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
            position: absolute;
            top: 8px;
            right: 12px;
            z-index: 2;
            font-weight: 500;
        }

        .fecha-solicitud {
            font-size: 10px;
            color:rgb(160, 158, 158);
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
            position: absolute;
            top: 26px;
            right: 7px;
            z-index: 2;
            font-weight: 400;
            font-family: 'Segoe UI', 'system-ui';
            font-variant-numeric: tabular-nums;
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
            #17a2b8 calc(var(--naranja, 15%) + var(--amarillo, 25%) + var(--verde, 35%)), #17a2b8 100%); transition: all 0.3s ease;
        }

        .kanban-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .kanban-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .kanban-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            /* Oculto fuera de pantalla independientemente del ancho */
            width: clamp(200px, 32vw, 432px);
            /* Máx 432 px, mínimo 304 px, 32 % viewport */
            height: 100%;
            background: #fff;
            z-index: 1000;
            transition: right 0.3s ease;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .kanban-sidebar.show {
            right: 0;
        }

        /* Contenido del sidebar con scroll */
        #sidebar-card-body {
            flex: 1 !important;
            overflow-y: auto !important;
            padding: 2rem !important;
            max-height: calc(100vh - 60px) !important;
            /* Altura menos header */
            display: flex !important;
            flex-direction: column !important;
        }

        /* Personalizar scrollbar del sidebar */
        #sidebar-card-body::-webkit-scrollbar {
            width: 8px;
        }

        #sidebar-card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #sidebar-card-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #sidebar-card-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Bloquear scroll de la página principal cuando sidebar está abierto */
        body.sidebar-open {
            overflow: hidden;
        }

        /* Optimización de items para el sidebar */
        #sidebar-card-body .item-selector {
            padding: 12px 45px 12px 16px !important;
            margin-bottom: 10px !important;
            position: relative !important;
            min-height: auto !important;
            height: auto !important;
            border: 1px solid #e3e6f0 !important;
            border-radius: 8px !important;
            background: white !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }

        #sidebar-card-body .item-body {
            display: block !important;
            width: 100% !important;
            padding-right: 0 !important;
        }

        #sidebar-card-body .item-info {
            width: 100% !important;
            flex: none !important;
        }

        #sidebar-card-body .item-name {
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            width: 100% !important;
            margin-bottom: 6px !important;
            display: block !important;
            color: #2c3e50 !important;
            font-weight: 500 !important;
        }

        #sidebar-card-body .item-desc {
            font-size: 0.8rem !important;
            margin-top: 0 !important;
            color: #6c757d !important;
            width: 100% !important;
            white-space: normal !important;
            text-align: left !important;
        }

        /* Responsive para pantallas más pequeñas */
        @media (max-width: 768px) {
            .kanban-sidebar {
                right: -100%;
                width: 100%;
            }
        }

        .solicitud-titulo {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: #2c3e50;
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

        .item-selector {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            padding: 20px;
            width: 92%;
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
            width: 18px;
            height: 18px;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 10;
        }

        .checkbox-indicator.checked {
            border-color: #007bff;
            background: #007bff;
        }

        .checkbox-indicator.checked::after {
            content: '✓';
            color: white;
            font-size: 11px;
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

        .kanban-sidebar,
        .kanban-sidebar * {
            max-width: none !important;
        }

        #sidebar-card-body .item-name,
        #sidebar-card-body .item-desc {
            white-space: normal !important;
            overflow: visible !important;
            word-break: break-word !important;
        }

        .badge-pill {
            padding-right: 0.7rem !important;
            padding-left: 0.7rem !important;
        }

        .badge {
            padding: 0.2rem 1rem;
            font-size: 0.7rem;
            font-weight: 410 !important;
        }

        .solicitud-card {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            cursor: move;
        }

        .sortable-drag {
            opacity: 0.8 !important;
            transform: rotate(3deg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .sortable-ghost {
            opacity: 0.3;
            background-color: #f1f3f5;
            border: 2px dashed #007bff;
        }
        .solicitud-card.animar-traslado {
            transition: transform 0.5s cubic-bezier(.4,2,.6,1), opacity 0.5s;
            opacity: 0;
            transform: translateX(60px) scale(0.97);
        }
        .solicitud-card.animar-llegada {
            animation: llegadaTarjeta 0.5s cubic-bezier(.4,2,.6,1);
        }
        @keyframes llegadaTarjeta {
            0% {
                opacity: 0;
                transform: translateX(-60px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }
    </style>
@endsection

@section('contenedor')

    {{-- ITEMS DESTINATARIOS PARA CADA ROL --}}
    <div class="row">
        <div class="col-12">
            @if (!auth()->user()->hasRole('Operador'))
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
                                            @php $operador_por_defecto = $operadores->random(); @endphp
                                            <span class="font-weight-600">{{ $operador_por_defecto->name }}</span>
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
                                        @php $operador_por_defecto = $operadores->random(); @endphp
                                        @foreach ($operadores as $operador)
                                            <div class="col-md-3">
                                                <div class="item-selector {{ $operador_por_defecto->id == $operador->id ? 'selected' : '' }}"
                                                    onclick="selectItem('operador_{{ $operador->id }}')">
                                                    <div class="item-body">
                                                        <div class="item-info">
                                                            <div class="item-name">{{ $operador->name }}</div>
                                                            <div class="item-desc">Operador calificado</div>
                                                        </div>
                                                        <div class="radio-indicator"></div>
                                                    </div>
                                                    <input type="radio" id="operador_{{ $operador->id }}"
                                                        name="operador_destino" value="{{ $operador->id }}"
                                                        {{ $operador_por_defecto->id == $operador->id ? 'checked' : '' }}
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
            @endif
        </div>
    </div>
    {{-- TABLEROS KANBAN --}}
    @php
        use App\Services\KeyRipper;
        $recibidas = $tarjetas->where('estado_id', 1);
        $progreso = $tarjetas->where('estado_id', 2);
        $resueltas = $tarjetas->where('estado_id', 3);
    @endphp
    <div class="row" style="display: flex; align-items: stretch;">
        {{-- RECIBIDAS --}}
        <div class="col-md-4"> 
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-archive text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Recibidas</h6>
                        <span class="badge badge-white ml-auto text-black-50" id="contador-recibidas">{{ $recibidas->count() }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-recibidas" class="sortable-column">
                        @forelse($recibidas as $recepcion)
                            <div class="solicitud-card" data-id="{{ $recepcion['recepcion_id'] }}"
                                data-atencion-id="{{ $recepcion['atencion_id'] }}"
                                data-estado-id="{{ $recepcion['estado_id'] }}" style="border-left-color: #ffc107;">
                                <div class="solicitud-titulo">
                                    @if ($recepcion['titulo'] && $recepcion['detalle'])
                                        {{ $recepcion['titulo'] }} - {{ $recepcion['detalle'] }}
                                    @elseif($recepcion['titulo'])
                                        {{ $recepcion['titulo'] }}
                                    @elseif($recepcion['detalle'])
                                        {{ $recepcion['detalle'] }}
                                    @else
                                        Sin título
                                    @endif
                                </div>
                                <div class="row"> {{-- ID Y FECHA --}}
                                    <div class="solicitud-id text-center">
                                        <small style="font-size: 0.7rem;">
                                            {{ KeyRipper::rip($recepcion['atencion_id']) }}
                                        </small>
                                    </div>
                                    <div class="fecha-solicitud">
                                        {{ \Carbon\Carbon::parse($recepcion['fecha_hora_solicitud'])->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="solicitud-estado" style="font-size: 11px; color:rgb(170, 95, 34) !important; margin-top: 5px;">
                                    Estado: {{ $recepcion['estado'] }}
                                </div>
                                <div class="progress-divider" data-atencion-id="{{ $recepcion['atencion_id'] }}" data-avance="{{ $recepcion['porcentaje_progreso'] }}"></div>
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
                                    
                                    <div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
                                        <div style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                            {{ $recepcion['user_name'] }}
                                        </div>
                                        <div
                                            style="text-align: right; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: 500; display: inline-block; margin-left: auto;">
                                            <span style="color:rgb(170, 95, 34) !important;" class="badge badge-pill badge-light-warning">{{ $recepcion['role_name'] }}</span>
                                            <span style="color: #612d03 !important; font-weight: 400;">del área {{ $recepcion['area'] }}</span> 
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
        {{-- PROGRESO --}}
        <div class="col-md-4"> 
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-time-five text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">En Progreso</h6>
                        <span class="badge badge-white ml-auto text-black-50" id="contador-progreso">{{ $progreso->count() }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-progreso" class="sortable-column">
                        @forelse($progreso as $recepcion)
                            <div class="solicitud-card" data-id="{{ $recepcion['recepcion_id'] }}"
                                data-atencion-id="{{ $recepcion['atencion_id'] }}"
                                data-estado-id="{{ $recepcion['estado_id'] }}" style="border-left-color: #17a2b8;">
                                <div class="solicitud-titulo">
                                    @if ($recepcion['titulo'] && $recepcion['detalle'])
                                        {{ $recepcion['titulo'] }} - {{ $recepcion['detalle'] }}
                                    @elseif($recepcion['titulo'])
                                        {{ $recepcion['titulo'] }}
                                    @elseif($recepcion['detalle'])
                                        {{ $recepcion['detalle'] }}
                                    @else
                                        Sin título
                                    @endif
                                </div>

                                <div class="row"> {{-- ID Y FECHA --}}
                                    <div class="solicitud-id text-center">
                                        <small style="font-size: 0.7rem;">
                                            {{ KeyRipper::rip($recepcion['atencion_id']) }}
                                        </small>
                                    </div>
                                    <div class="fecha-solicitud">
                                        {{ \Carbon\Carbon::parse($recepcion['fecha_hora_solicitud'])->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="solicitud-estado" style="font-size: 11px; color: #17a2b8; margin-top: 5px;">
                                    Estado: {{ $recepcion['estado'] }}
                                </div>
                                <div class="progress-divider" data-atencion-id="{{ $recepcion['atencion_id'] }}" data-avance="{{ $recepcion['porcentaje_progreso'] }}"></div>
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">

                                    <div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
                                        <div style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                            {{ $recepcion['user_name'] }}
                                        </div>
                                        <div
                                            style="text-align: right; padding: 1px 6px; border-radius: 3px; font-size: 9px; display: inline-block; margin-left: auto;">
                                            <span style="color:rgb(58, 121, 194) !important;" class="badge badge-pill badge-light-primary">{{ $recepcion['role_name'] }}</span>
                                            <span style="color:rgb(11, 62, 119) !important; font-weight: 500;">del área {{ $recepcion['area'] }}</span> 
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
        {{-- RESUELTAS --}}
        <div class="col-md-4"> 
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-check-circle text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Resueltas</h6>
                        <span class="badge badge-white ml-auto text-black-50" id="contador-resueltas">{{ $resueltas->count() }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-resueltas" class="sortable-column">
                        @forelse($resueltas as $recepcion)
                            <div class="solicitud-card" data-id="{{ $recepcion['recepcion_id'] }}"
                                data-atencion-id="{{ $recepcion['atencion_id'] }}"
                                data-estado-id="{{ $recepcion['estado_id'] }}" style="border-left-color: #28a745;">
                                <div class="solicitud-titulo">
                                    @if ($recepcion['titulo'] && $recepcion['detalle'])
                                        {{ $recepcion['titulo'] }} - {{ $recepcion['detalle'] }}
                                    @elseif($recepcion['titulo'])
                                        {{ $recepcion['titulo'] }}
                                    @elseif($recepcion['detalle'])
                                        {{ $recepcion['detalle'] }}
                                    @else
                                        Sin título
                                    @endif
                                </div>
                                <div class="row"> {{-- ID Y FECHA --}}
                                    <div class="solicitud-id text-center">
                                        <small style="font-size: 0.7rem;">
                                            {{ KeyRipper::rip($recepcion['atencion_id']) }}
                                        </small>
                                    </div>
                                    <div class="fecha-solicitud">
                                        {{ \Carbon\Carbon::parse($recepcion['fecha_hora_solicitud'])->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="solicitud-estado" style="font-size: 11px; color: #28a745; margin-top: 5px;">
                                    Estado: {{ $recepcion['estado'] }}
                                </div>
                                <div class="progress-divider" data-atencion-id="{{ $recepcion['atencion_id'] }}" data-avance="{{ $recepcion['porcentaje_progreso'] }}"></div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
                                    
                                    <div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
                                        <div
                                            style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                            {{ $recepcion['user_name'] }}
                                        </div>
                                        <div
                                            style="text-align: right; padding: 1px 6px; border-radius: 3px; font-size: 9px; display: inline-block; margin-left: auto;">
                                            <span style="color:rgb(10, 95, 30) !important;" class="badge badge-pill badge-light-success">{{ $recepcion['role_name'] }}</span>
                                            <span style="color:rgb(10, 95, 30) !important; font-weight: 400;">del área {{ $recepcion['area'] }}</span> 
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
        <div id="sidebar-card-body">
            <p>Selecciona una tarjeta para ver detalles...</p>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/jkanban/Sortable.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/jkanban/jkanban.min.js') }}"></script>
    <script>
        //SELECCIONANDO EL ITEM DESTINATARIO
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
        //INICIALIZAR KANBAN
        function initKanban() { // Inicializar el kanban
            const columnas = ['columna-recibidas', 'columna-progreso', 'columna-resueltas'];
            columnas.forEach(function(columnaId) {
                const elemento = document.getElementById(columnaId);
                if (!elemento) return;
                new Sortable(elemento, {
                    group: 'kanban', 
                    animation: 150, 
                    ghostClass: 'sortable-ghost', 
                    chosenClass: 'sortable-chosen', 
                    dragClass: 'sortable-drag', 
                    forceFallback: true, 
                    fallbackOnBody: true,
                    fallbackClass: 'sortable-fallback',
                    preventFallback: true,
                    supportPointer: true,
                    onChoose: function(evt) { // Prevenir selección de texto
                        evt.preventDefault();
                        evt.stopPropagation();
                    },
                    onMove: function(evt) { // Configuraciones adicionales para mejorar el drag
                        evt.preventDefault();
                        evt.stopPropagation();
                        document.querySelectorAll('.sortable-column').forEach(col => {
                            col.style.borderColor = 'transparent';
                        });
                        evt.to.style.borderColor = '#d1ecf1'; // Azul muy tenue
                        evt.to.style.backgroundColor = '#f8fdff'; // Fondo casi imperceptible
                    },
                    onEnd: function(evt) { 
                        document.querySelectorAll('.sortable-column').forEach(col => { // Restaurar apariencia de las columnas
                            col.style.borderColor = col.children.length === 0 ? '#e9ecef' : 'transparent';
                            col.style.backgroundColor = col.children.length === 0 ? '#f8f9fa' : 'transparent';
                        });
                        const solicitudId = evt.item.dataset.id;
                        const columnaOrigen = evt.from.id;
                        const columnaDestino = evt.to.id;
                        if (columnaOrigen !== columnaDestino) { // Verificar si realmente cambió de columna
                            if (columnaOrigen !== 'columna-recibidas' || columnaDestino !== 'columna-progreso') { // Validar movimiento único desde columna-recibidas hacia columna-progreso
                                toastr.error('Movimiento no disponible', 'warning', {
                                    positionClass: 'toast-top-right',
                                    timeOut: 1000
                                });

                                $(evt.from).append(evt.item); // Revertir la tarjeta a su posición original
                                return;
                            }
                            updatePosition(solicitudId, columnaDestino, evt);
                        }
                        actualizarMensajeColumnaVacia(); // NUEVO: actualizar mensajes de columnas vacías
                    },
                    onStart: function(evt) { // Estilos para evitar selección
                        evt.target.style.userSelect = 'none';
                        evt.target.style.webkitUserSelect = 'none';
                        evt.target.style.mozUserSelect = 'none';
                        evt.target.style.msUserSelect = 'none';
                    }
                });
            });
        }
        function updatePosition(solicitudId, nuevaColumna, evt) {
            //ACTUALIZACION DE ESTADO DE LA SOLICITUD EN EL FRONTEND
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
            //ACTUALIZAR ESTADO DE LA SOLICITUD EN EL BACKEND
            let url = ''; //Seleccionando la ruta a la que se va a enviar la solicitud
            let selectedValue = null;
            if (userRole === 'Recepcionista') { //Derivar
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
            } else if (userRole === 'Supervisor') { //Asignar
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
            } else if (userRole === 'Gestor') { //Delegar
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
            } else if (userRole === 'Operador') { //Iniciar tareas
                url = '{{ route('recepcion.iniciar-tareas', ['recepcion_id' => ':id']) }}'
                    .replace(':id', solicitudId);
            }
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
                            title: 'Solicitud #' + String(solicitudId).slice(-3) + ' "' +
                                tituloTarjeta + '" 👉🏻 ' + nombreEstado,
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
                        actualizarMensajeColumnaVacia(); // Actualizar mensaje de columna vacía
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
                    actualizarMensajeColumnaVacia(); // Actualizar mensaje de columna vacía
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
            $('body').addClass('sidebar-open'); // Bloquear scroll de la página principal
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
                } else if (tarea.estado_id == 3) { // Completada (ID 3 = Resuelta) - usando == por si es string
                    estadoColor = '#28a745';
                    estadoIcon = 'bx-check-circle';
                }
                tareasHtml += `
                    <div class="item-selector ${tarea.estado_id == 3 ? 'selected' : ''}" onclick="selectTask('task_${tarea.actividad_id}')">
                        <div class="checkbox-indicator ${tarea.estado_id == 3 ? 'checked' : ''}" id="checkbox_${tarea.actividad_id}"></div>
                        <div class="item-body">
                            <div class="item-info">
                                <div class="item-name">${tarea.tarea}</div>
                                <div class="item-desc">${tarea.actividad_id}</div>
                            </div>
                        </div>
                        <input type="checkbox" id="task_${tarea.actividad_id}" name="tarea_completada" 
                            value="${tarea.actividad_id}" ${tarea.estado_id == 3 ? 'checked' : ''} style="display: none;">
                    </div>
                `;
            });
            tareasHtml += '</div>';
            $('#sidebar-card-body').append(tareasHtml);
        }
        //ACTUALIZAR EL ESTADO DE LA TAREA
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
                            updateProgressByPercentage(response.atencion_id, response.progreso.porcentaje);
                            if (response.todas_resueltas && response
                                .solicitud_actualizada) { // Verificar si todas las tareas están resueltas
                                const tarjeta = $(
                                `.solicitud-card[data-id="${response.recepcion_id}"]`); // Mover la tarjeta al tablero de resueltas
                                if (tarjeta.length > 0) {
                                    tarjeta.css('border-left-color',
                                    '#28a745'); // Actualizar el estado visual de la tarjeta
                                    tarjeta.find('.solicitud-estado').text('Estado: Resuelta');
                                    tarjeta.find('.solicitud-estado').css('color', '#28a745');
                                    tarjeta.attr('data-estado-id', 3);
                                    $('#columna-resueltas').append(
                                    tarjeta); // Mover la tarjeta al tablero de resueltas
                                    actualizarContadores();
                                    Swal.fire({ // Mostrar mensaje de éxito
                                        position: 'top-end',
                                        type: 'success',
                                        title: '¡Todas las tareas completadas! Solicitud movida a Resueltas',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        confirmButtonClass: 'btn btn-primary',
                                        buttonsStyling: false
                                    });
                                }
                            } else {
                                Swal.fire({ // Mensaje normal para tarea individual
                                    position: 'top-end',
                                    type: 'success',
                                    title: 'Tarea ' + String(actividadId).slice(-4) +
                                        ' se reportó como ' + nuevoEstado,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    confirmButtonClass: 'btn btn-primary',
                                    buttonsStyling: false
                                });
                            }
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
                $('body').removeClass('sidebar-open'); // Reactivar scroll de la página principal
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
        //ACTUALIZAR PROGRESO DE LAS TAREAS
        function updateProgressByPercentage(atencionId, porcentaje) {
            let naranja, amarillo, verde, celeste;
            if (porcentaje == 0) { 
                naranja = 65;
                amarillo = 30;
                verde = 5;
                celeste = 0;
            } else if (porcentaje >= 10 && porcentaje < 30) {
                naranja = 45;
                amarillo = 35;
                verde = 15;
                celeste = 5;
            } else if (porcentaje >= 30 && porcentaje < 60) {
                naranja = 15;
                amarillo = 45;
                verde = 25;
                celeste = 15;
            } else if (porcentaje >= 60 && porcentaje < 80) {
                naranja = 0;
                amarillo = 35;
                verde = 40;
                celeste = 35;
            } else if (porcentaje >= 80 && porcentaje < 90) {
                naranja = 0;
                amarillo = 5;
                verde = 50;
                celeste = 45;
            } else if (porcentaje >= 90 && porcentaje < 100) {
                naranja = 0;
                amarillo = 0;
                verde = 15;
                celeste = 85;
            } else { // 100%
                naranja = 0;
                amarillo = 0;
                verde = 0;
                celeste = 100;
            }
            const progressBars = $(`[data-atencion-id="${atencionId}"]`); // Actualizar todas las barras de progreso con el mismo atencion_id
            if (progressBars.length > 0) {
                progressBars.css({
                    '--naranja': naranja + '%',
                    '--amarillo': amarillo + '%',
                    '--verde': verde + '%'
                });
            }
        }
        function actualizarMensajeColumnaVacia() { // NUEVO: Mostrar u ocultar mensaje de columna vacía
            const columnas = [
                {
                    id: 'columna-recibidas',
                    mensaje: '<div class="text-center text-muted py-4"><i class="bx bx-archive text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin solicitudes recibidas</div></div>'
                },
                {
                    id: 'columna-progreso',
                    mensaje: '<div class="text-center text-muted py-4"><i class="bx bx-time-five text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin tareas en progreso</div></div>'
                },
                {
                    id: 'columna-resueltas',
                    mensaje: '<div class="text-center text-muted py-4"><i class="bx bx-check-circle text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin tareas completadas</div></div>'
                }
            ];
            columnas.forEach(function(col) {
                const columna = document.getElementById(col.id);
                if (!columna) return;
                
                $(columna).find('.text-center.text-muted.py-4').remove(); // Eliminar mensajes existentes
                if ($(columna).find('.solicitud-card').length === 0) { // Si no hay tarjetas, agregar el mensaje
                    $(columna).append(col.mensaje);
                }
            });
        }
        function actualizarContadores() { // Función para actualizar los contadores de las columnas
            const recibidas = $('#columna-recibidas .solicitud-card').length;
            const progreso = $('#columna-progreso .solicitud-card').length;
            const resueltas = $('#columna-resueltas .solicitud-card').length;
            $('#contador-recibidas').text(recibidas);
            $('#contador-progreso').text(progreso);
            $('#contador-resueltas').text(resueltas);
            actualizarMensajeColumnaVacia(); // NUEVO: actualizar mensajes de columnas vacías
        }
        function initializeProgress() { // Función para inicializar progreso de todas las tarjetas
            @foreach ($tarjetas as $tarjeta)
                updateProgressByPercentage('{{ $tarjeta['atencion_id'] }}', {{ $tarjeta['porcentaje_progreso'] }});
            @endforeach
            actualizarMensajeColumnaVacia(); // NUEVO: inicializar mensajes de columnas vacías
        }
        // ESCUCHADOR DE AVANCES: Recoger IDs de tarjetas en tableros
        function obtenerAtencionIdsTableros() {
            let ids = [];
            $('.solicitud-card').each(function() {
                let atencionId = $(this).attr('data-atencion-id');
                if (atencionId) {
                    ids.push(atencionId);
                }
            });
            return [...new Set(ids)]; // Eliminar repetidos usando Set
        }
        function consultarAvancesTablero() {
            let atencionIds = obtenerAtencionIdsTableros();
            if (atencionIds.length === 0) {
                return; // No hay tarjetas en los tableros
            }
            $.post({
                url: '{{ route("recepcion.avance-tablero") }}',
                data: { 
                    atencion_ids: atencionIds, 
                    _token: $('meta[name="csrf-token"]').attr('content') 
                },
                success: function(data) {
                    data.forEach(function(item) { // Comparar avances y estado_id del backend con los del frontend
                        let $divider = $('.progress-divider[data-atencion-id="' + item.atencion_id + '"]');
                        let $card = $('.solicitud-card[data-atencion-id="' + item.atencion_id + '"]');
                        if ($divider.length > 0 && $card.length > 0) {
                            let avanceFrontend = parseFloat($divider.attr('data-avance') || '0'); // Avance
                            let avanceBackend = parseFloat(item.avance || '0');
                            if (avanceFrontend !== avanceBackend) {
                                $divider.attr('data-avance', avanceBackend);
                                updateProgressByPercentage(item.atencion_id, avanceBackend);
                            }
                            let estadoFrontend = parseInt($card.attr('data-estado-id'), 10); // Estado
                            let estadoBackend = parseInt(item.estado_id, 10);
                            if (estadoFrontend !== estadoBackend) {
                                $card.attr('data-estado-id', estadoBackend);
                                if (estadoBackend === 3) {
                                    $card.addClass('animar-traslado');
                                    setTimeout(function() {
                                        $card.removeClass('animar-traslado');
                                        $('#columna-resueltas').append($card);
                                        $card.addClass('animar-llegada');
                                        setTimeout(function() {
                                            $card.removeClass('animar-llegada');
                                        }, 500);
                                        $card.css('border-left-color', '#28a745');
                                        $card.find('.solicitud-estado').text('Estado: Resuelta');
                                        $card.find('.solicitud-estado').css('color', '#28a745');
                                        actualizarContadores();
                                    }, 500);
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al consultar avances:', error);
                }
            });
        }
        setInterval(consultarAvancesTablero, 10000); // Iniciar escuchador
        function obtenerUltimoIdRecibidas() {
            let ids = $('#columna-recibidas .solicitud-card').map(function() {
                return parseInt($(this).attr('data-id'), 10);
            }).get();
            return ids.length ? Math.max(...ids) : null;
        }
        function cargarNuevasRecibidas() {
            let ultimoId = obtenerUltimoIdRecibidas();
            $.post({
                url: '{{ route("recepcion.nuevas-recibidas") }}',
                data: {
                    ultimo_id: ultimoId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(nuevas) {
                    if (nuevas && nuevas.length > 0) {
                        nuevas.forEach(function(tarjeta) {
                            let html = `
                                <div class="solicitud-card animar-llegada" data-id="${tarjeta.recepcion_id}"
                                    data-atencion-id="${tarjeta.atencion_id}"
                                    data-estado-id="${tarjeta.estado_id}" style="border-left-color: #ffc107;">
                                    <div class="solicitud-titulo">${tarjeta.titulo || 'Sin título'}</div>
                                    <div class="row"> {{-- ID Y FECHA --}}
                                        <div class="solicitud-id text-center">
                                            <small style="font-size: 0.7rem;">
                                                ${tarjeta.solicitud_id_ripped}
                                            </small>
                                        </div>
                                        <div class="fecha-solicitud">
                                            ${tarjeta.fecha_relativa}
                                        </div>
                                    </div>
                                    <div class="solicitud-estado" style="font-size: 11px; color:rgb(170, 95, 34) !important; margin-top: 5px;">
                                        Estado: ${tarjeta.estado} (atencion_id: ${tarjeta.atencion_id})
                                    </div>
                                    <div class="progress-divider" data-atencion-id="${tarjeta.atencion_id}" data-avance="${tarjeta.porcentaje_progreso}"></div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px;">
                                        <div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
                                            <div style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                                ${tarjeta.user_name}
                                            </div>
                                            <div style="text-align: right; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: 500; display: inline-block; margin-left: auto;">
                                                <span style="color:rgb(170, 95, 34) !important;" class="badge badge-pill badge-light-warning">${tarjeta.role_name}</span>
                                                <span style="color: #612d03 !important; font-weight: 400;">del área ${tarjeta.area}</span>
                                            </div>
                                        </div>
                                        <div style="margin-left: 8px;">
                                            <img src="${tarjeta.user_foto}" alt="Usuario" class="avatar"
                                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                                        </div>
                                    </div>
                                </div>
                            `;
                            let $nueva = $(html);
                            $('#columna-recibidas').prepend($nueva);
                            updateProgressByPercentage(tarjeta.atencion_id, tarjeta.porcentaje_progreso);
                            setTimeout(function() {
                                $nueva.removeClass('animar-llegada');
                            }, 500);
                        });
                        actualizarContadores();
                    }
                }
            });
        }
        setInterval(cargarNuevasRecibidas, 10000); // Iniciar escuchador
        $(document).ready(function() { // Inicializar al cargar la página
            initializeProgress();
            initKanban();
        });
    </script>
@endsection
