@extends('dashboard')

@section('css')
<!-- SweetAlert2 CSS Local -->
@vite([
'resources/css/app-assets/vendors/css/extensions/sweetalert2.min.css',
'resources/css/app-assets/css/bootstrap-extended.css',
'resources/css/app-assets/css/pages/app-kanban.css'
])
<style>
    .btn i {
        position: relative;
        top: 1px;
    }

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
        color: rgb(255, 255, 255);
        background: #87898b;
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
        color: rgb(160, 158, 158);
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

    .kanban-columna {
        min-height: 400px;
        padding: 10px;
    }

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
        border: 2px dashed transparent;
        border-radius: 8px;
        transition: all 0.3s ease;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Mejorar feedback visual en touch */
    .sortable-ghost {
        opacity: 0.5 !important;
        background: #f8f9fa !important;
        visibility: visible !important;
    }

    .sortable-chosen {
        opacity: 1 !important;
        background: #e3f2fd !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        visibility: visible !important;
        display: block !important;
    }

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

    .progress-divider {
        height: 3px;
        width: 100%;
        border-radius: 1.5px;
        margin: 8px 0 6px 0;
        background: linear-gradient(to right,
                #ff8c00 0%, #ff8c00 var(--naranja, 15%),
                #ffd700 var(--naranja, 15%), #ffd700 calc(var(--naranja, 15%) + var(--amarillo, 25%)),
                #28a745 calc(var(--naranja, 15%) + var(--amarillo, 25%)), #28a745 calc(var(--naranja, 15%) + var(--amarillo, 25%) + var(--verde, 35%)),
                #17a2b8 calc(var(--naranja, 15%) + var(--amarillo, 25%) + var(--verde, 35%)), #17a2b8 100%);
        transition: all 0.3s ease;
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
        width: clamp(200px, 32vw, 432px);
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

    #sidebar-card-body {
        flex: 1 !important;
        overflow-y: auto !important;
        padding: 1rem !important;
        max-height: calc(100vh - 60px) !important;
        display: flex !important;
        flex-direction: column !important;
    }

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

    body.sidebar-open {
        overflow: hidden;
    }

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
        width: 92%;
        height: 100%;
    }

    .checkbox-indicator {
        background: white;
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
    }

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
        max-width: 24rem !important;
    }

    .solicitud-card {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        cursor: move;
    }

    .solicitud-card.animar-traslado {
        transition: transform 0.5s cubic-bezier(.4, 2, .6, 1), opacity 0.5s;
        opacity: 0;
        transform: translateX(60px) scale(0.97);
    }

    .solicitud-card.animar-llegada {
        animation: llegadaTarjeta 0.5s cubic-bezier(.4, 2, .6, 1);
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
                                    <span class="font-weight-600">{{ auth()->user()->equipos()->first()->equipo
                                        }}</span>
                                    @elseif(auth()->user()->hasRole('Gestor'))
                                    @php $operador_por_defecto = $operadores->random(); @endphp
                                    <span class="font-weight-600">{{ $operador_por_defecto->name }}</span>
                                    @endif
                                </h6>
                                <small class="text-white-50" style="font-size: 0.8rem;">
                                    Selecciona el item destino para impulsar las solicitudes
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
                                    <div class="selectable-item {{ $area->id == auth()->user()->area_id ? 'selected' : '' }}"
                                        onclick="selectItem('area_{{ $area->id }}')">
                                        <div class="item-body">
                                            <div class="item-info">
                                                <div class="item-name">{{ $area->area }}</div>
                                                <div class="item-desc">Área de trabajo</div>
                                            </div>
                                            <div class="radio-indicator"></div>
                                        </div>
                                        <input type="radio" id="area_{{ $area->id }}" name="area_destino"
                                            value="{{ $area->id }}" {{ $area->id == auth()->user()->area_id ? 'checked'
                                        : '' }}
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
                                    <div class="selectable-item {{ $equipo->id == auth()->user()->equipos->first()->id ? 'selected' : '' }}"
                                        onclick="selectItem('equipo_{{ $equipo->id }}')">
                                        <div class="item-body">
                                            <div class="item-info">
                                                <div class="item-name">{{ $equipo->equipo }}</div>
                                                <div class="item-desc">Equipo de trabajo</div>
                                            </div>
                                            <div class="radio-indicator"></div>
                                        </div>
                                        <input type="radio" id="equipo_{{ $equipo->id }}" name="equipo_destino"
                                            value="{{ $equipo->id }}" {{ $equipo->id ==
                                        auth()->user()->equipos->first()->id ? 'checked' : '' }}
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
                                    <div class="selectable-item {{ $operador_por_defecto->id == $operador->id ? 'selected' : '' }}"
                                        onclick="selectItem('operador_{{ $operador->id }}')">
                                        <div class="item-body">
                                            <div class="item-info">
                                                <div class="item-name">{{ $operador->name }}</div>
                                                <div class="item-desc">Operador calificado</div>
                                            </div>
                                            <div class="radio-indicator"></div>
                                        </div>
                                        <input type="radio" id="operador_{{ $operador->id }}" name="operador_destino"
                                            value="{{ $operador->id }}" {{ $operador_por_defecto->id == $operador->id ?
                                        'checked' : '' }}
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
<div class="row kanban-container" style="display: flex; align-items: stretch;">
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
                    <div class="ml-auto d-flex align-items-center">
                        @if(auth()->user()->hasRole('Gestor') || auth()->user()->hasRole('Supervisor'))
                        <button type="button" class="btn btn-sm btn-outline-light mr-2" id="btn-distribuir-todas"
                            data-toggle="tooltip" data-placement="top" title="Impulsar todas las solicitudes"
                            style="border: 1px solid rgba(255,255,255,0.3); background: transparent; padding: 4px 8px; font-size: 0.8rem;">
                            <i class="bx bxs-send" style="font-size: 0.8rem;"></i>
                        </button>
                        @endif
                        <span class="badge badge-white text-dark" id="contador-recibidas">{{ count($recibidas) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                <div id="columna-recibidas" class="sortable-column">
                    {{-- Las tarjetas se dibujarán con JavaScript --}}
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
                    <span class="badge badge-white ml-auto text-dark" id="contador-progreso">{{ count($progreso)
                        }}</span>
                </div>
            </div>
            <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                <div id="columna-progreso" class="sortable-column">
                    {{-- Las tarjetas se dibujarán con JavaScript --}}
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
                    <span class="badge badge-white ml-auto text-dark" id="contador-resueltas">{{ count($resueltas)
                        }}</span>
                </div>
            </div>
            <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                <div id="columna-resueltas" class="sortable-column">
                    {{-- Las tarjetas se dibujarán con JavaScript --}}
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
<!-- BEGIN: Critical JavaScript (Emergency Load) -->
@vite([
'resources/css/app-assets/vendors/js/extensions/sweetalert2.all.min.js',
'resources/css/app-assets/vendors/js/jkanban/Sortable.min.js',
'resources/css/app-assets/vendors/js/jkanban/jkanban.min.js'
])
<!-- END: Critical JavaScript (Emergency Load) -->
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
            document.querySelectorAll('.selectable-item').forEach(selector => { // Desmarcar todos los selectores
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
                    onEnd: function(evt) {
                        const solicitudId = evt.item.dataset.id;
                        const columnaOrigen = evt.from.id;
                        const columnaDestino = evt.to.id;
                        if (columnaOrigen !==
                            columnaDestino) { // Verificar si realmente cambió de columna
                            if (columnaOrigen !== 'columna-recibidas' || columnaDestino !==
                                'columna-progreso'
                            ) { // Validar movimiento único desde columna-recibidas hacia columna-progreso
                                toastr.error('Movimiento no disponible', 'warning', {
                                    positionClass: 'toast-top-right',
                                    timeOut: 1000
                                });
                                $(evt.from).append(evt
                                    .item); // Revertir la tarjeta a su posición original
                                return;
                            }
                            updatePosition(solicitudId, columnaDestino,
                                evt
                            ); //Actualizar el drag and drop tanto en el backend como en el frontend
                        }
                        actualizarMensajeColumnaVacia();
                    }
                });
            });
        }
        $('[data-toggle="popover"]').popover({ // Inicializar popovers de Bootstrap
            html: true,
            container: 'body',
            trigger: 'hover'
        });

        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Manejar click del botón delegar todas
        $(document).on('click', '#btn-distribuir-todas', function() {
            let recepcionIds = []; // Recolectar todas las tarjetas que se encuentren en la bandeja "recibidas"
            $('#columna-recibidas .solicitud-card').each(function() {
                let recepcionId = $(this).data('id');
                if (recepcionId && recepcionId !== 'null' && recepcionId !== 'undefined') {
                    recepcionIds.push(recepcionId);
                }
            });
            if (recepcionIds.length === 0) {
                Swal.fire({
                    position: 'top-end',
                    type: 'warning',
                    title: 'No hay solicitudes en la bandeja de recibidas',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }
            Swal.fire({
                title: '¿Distribuir todas las solicitudes?',
                text: 'Esta acción distribuirá ${recepcionIds.length} solicitudes recibidas.',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, delegar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.value === true) {
                    // Determinar la URL según el rol del usuario
                    let url = '';
                    @if(auth()->user()->hasRole('Gestor'))
                        url = '{{ route('recepcion.delegar-todas') }}';
                    @elseif(auth()->user()->hasRole('Supervisor'))
                        url = '{{ route('recepcion.asignar-todas') }}';
                    @endif

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            recepcion_ids: recepcionIds
                        },
                        success: function(response) {
                            if (response.success) {
                                // Mostrar mensaje principal de éxito
                                Swal.fire({
                                    position: 'top-end',
                                    type: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                
                                // Mover tarjetas procesadas exitosamente al tablero "En progreso"
                                const tarjetasProcesadas = response.tarjetas_delegadas || response.tarjetas_asignadas || [];
                                if (tarjetasProcesadas.length > 0) {
                                    tarjetasProcesadas.forEach(function(recepcionId) {
                                        const tarjeta = $(`.solicitud-card[data-id="${recepcionId}"]`);
                                        if (tarjeta.length > 0) {
                                            // Mover la tarjeta al tablero "En progreso"
                                            $('#columna-progreso').append(tarjeta);
                                            
                                            // Actualizar el color del estado
                                            const estadoElement = tarjeta.find('.solicitud-estado');
                                            estadoElement.css({
                                                'color': '#17a2b8',
                                                'font-size': '0.8em',
                                                'margin-top': '5px'
                                            });
                                        }
                                    });
                                }
                                
                                // Mostrar solo los primeros 5 errores con toastr sin tiempo de cierre
                                if (response.errores && response.errores.length > 0) {
                                    const primerosErrores = response.errores.slice(0, 5);
                                    primerosErrores.forEach(function(error) {
                                        toastr.error(error, '', {
                                            timeOut: 0,
                                            extendedTimeOut: 0,
                                            closeButton: true,
                                            preventDuplicates: false,
                                            newestOnTop: true,
                                            autoDismiss: false,
                                            tapToDismiss: false,
                                            hideDuration: 0,
                                            showDuration: 0
                                        });
                                    });
                                }
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    type: 'error',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                position: 'top-end',
                                type: 'error',
                                title: 'Error al delegar solicitudes',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    });
                }
            });
        });

        //ACTUALIZAR EL MOVIMIENTO DE LA TARJETA, TANTO EN EL BACKEND Y COMO EN EL FRONTEND
        function updatePosition(solicitudId, nuevaColumna, evt) {
            let nuevoEstadoId = 1; //Iniciando parametros
            let nombreEstado = 'Recibida';
            let colorBorde = 'badge-secondary';
            let estadoColor = 'rgb(170, 95, 34)'; // Color por defecto
            switch (nuevaColumna) {
                case 'columna-recibidas':
                    nuevoEstadoId = 1;
                    nombreEstado = 'Recibida';
                    colorBorde = 'badge-secondary';
                    estadoColor = '#2c3e50';
                    break;
                case 'columna-progreso':
                    nuevoEstadoId = 2;
                    nombreEstado = 'En progreso';
                    colorBorde = 'badge-primary';
                    estadoColor = '#17a2b8';
                    break;
                case 'columna-resueltas':
                    nuevoEstadoId = 3;
                    nombreEstado = 'Resuelta';
                    colorBorde = 'badge-success';
                    estadoColor = '#28a745';
                    break;
            }
            let url = ''; //Seleccionando la ruta a la que se va a enviar la solicitud
            let selectedValue = null;
            if (userRole === 'Recepcionista') { //Derivar
                selectedValue = $('input[name="area_destino"]:checked').val();
                if (!selectedValue) {
                    Swal.fire({
                        position: 'top-end',
                        type: 'warning',
                        title: 'Debes seleccionar un área destino',
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
                        title: 'Debes seleccionar un equipo destino',
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
                        title: 'Debes seleccionar un operador destino',
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
            $.ajax({ //Enviando la solicitud a la ruta seleccionada
                url: url,
                method: 'POST',
                cache: false,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const tarjeta = $(`.solicitud-card[data-id="${solicitudId}"]`);
                        const tituloTarjeta = tarjeta.find('.solicitud-titulo').text() || 'Sin título';
                        if (tarjeta.length > 0) {
                            // Actualizar estilos básicos de la tarjeta
                            tarjeta.removeClass(
                                'border-badge-secondary border-badge-primary border-badge-success border-badge-danger border-badge-warning'
                            );
                            tarjeta.addClass('border-' + colorBorde);
                            tarjeta.find('.solicitud-estado').text('Estado: ' + nombreEstado);
                            tarjeta.find('.solicitud-estado').css({
                                'color': estadoColor,
                                'font-size': '11px',
                                'margin-top': '5px'
                            });
                            tarjeta.attr('data-estado-id', nuevoEstadoId);

                            // Determinar clase de badge según el nuevo estado
                            let badgeColor = 'badge-secondary'; // Por defecto
                            if (nuevoEstadoId == 3) { // Resuelta
                                badgeColor = 'badge-success';
                            } else if (nuevoEstadoId == 2) { // En progreso
                                badgeColor = 'badge-primary';
                            } else if (nuevoEstadoId == 1) { // Recibida
                                badgeColor = 'badge-secondary';
                            }

                            // Actualizar badges en los popovers
                            tarjeta.find('[data-toggle="popover"]').each(function() {
                                let $popover = $(this);
                                let currentContent = $popover.attr('data-content');
                                if (currentContent) {
                                    let newContent = currentContent.replace(/badge-\w+/g, badgeColor);
                                    $popover.attr('data-content', newContent);
                                }
                            });
                        }
                        toastr.success(response.message, '', {
                            positionClass: 'toast-top-right',
                            closeButton: true,
                            timeOut: 15000
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
            
            // Limpiar clases de drag and drop al abrir el overlay
            limpiarClasesDrag();
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
                    <div class="selectable-item ${tarea.estado_id == 3 ? 'selected' : ''}" onclick="selectTask('task_${tarea.actividad_id}')">
                        <div class="checkbox-indicator ${tarea.estado_id == 3 ? 'checked' : ''}" id="checkbox_${tarea.actividad_id}"></div>
                        <div class="item-body">
                            <div class="item-info">
                                <div class="item-name">${tarea.tarea}</div>
                                <div class="item-desc">T-${tarea.actividad_id_ripped}</div>
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
            const selectableItem = document.querySelector(`[onclick="selectTask('${taskId}')"]`);
            if (checkbox && visualCheckbox && selectableItem) {
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    visualCheckbox.classList.add('checked');
                    selectableItem.classList.add('selected');
                } else {
                    visualCheckbox.classList.remove('checked');
                    selectableItem.classList.remove('selected');
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
                                    `.solicitud-card[data-id="${response.recepcion_id}"]`
                                ); // Mover la tarjeta al tablero de resueltas
                                if (tarjeta.length > 0) {
                                    tarjeta.css('border-left-color',
                                        '#28a745'); // Actualizar el estado visual de la tarjeta
                                    tarjeta.find('.solicitud-estado').text('Estado: Resuelta');
                                    tarjeta.find('.solicitud-estado').css({
                                        'color': '#28a745',
                                        'font-size': '11px',
                                        'margin-top': '5px'
                                    });
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
                
                // Limpiar clases de rotación y drag que puedan haber quedado
                $('.solicitud-card').removeClass('dragging sortable-drag sortable-chosen sortable-ghost');
                $('.sortable-fallback').remove();
            });
        
        // Función para limpiar clases de drag and drop
        function limpiarClasesDrag() {
            $('.solicitud-card').removeClass('dragging sortable-drag sortable-chosen sortable-ghost');
            $('.sortable-fallback').remove();
        }
        
        // Limpiar clases cuando se presiona Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                limpiarClasesDrag();
            }
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
        //FUNCIONES PARA ACTUALIZAR BARRAS DE PROGRESO
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
            const progressBars = $(
                `[data-atencion-id="${atencionId}"]`); // Actualizar todas las barras de progreso con el mismo atencion_id
            if (progressBars.length > 0) {
                progressBars.css({
                    '--naranja': naranja + '%',
                    '--amarillo': amarillo + '%',
                    '--verde': verde + '%'
                });
            }
        }
        function actualizarMensajeColumnaVacia() { // NUEVO: Mostrar u ocultar mensaje de columna vacía
            const columnas = [{
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
                url: '{{ route('recepcion.avance-tablero') }}',
                data: {
                    atencion_ids: atencionIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    data.forEach(function(
                        item) { // Comparar avances y estado_id del backend con los del frontend
                        let $divider = $('.progress-divider[data-atencion-id="' + item.atencion_id +
                            '"]');
                        let $card = $('.solicitud-card[data-atencion-id="' + item.atencion_id + '"]');
                        if ($divider.length > 0 && $card.length > 0) {
                            let avanceFrontend = parseFloat($divider.attr('data-avance') ||
                                '0'); // Avance
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
                                    let debeTrasladar =
                                        true; // Verificar si el rol del usuario destino NO es Gestor
                                    // Solo verificar el rol del usuario que está trabajando actualmente en la tarjeta
                                    if (item.recepciones && item.recepciones.length > 0) {
                                        // Buscar la recepción que corresponde al usuario actual
                                        let recepcionActual = item.recepciones.find(function(
                                            recepcion) {
                                            return recepcion.usuarioDestino && recepcion
                                                .usuarioDestino.id === item.usuario_actual_id;
                                        });

                                        if (recepcionActual && recepcionActual.role && recepcionActual
                                            .role.name === 'Gestor') {
                                            debeTrasladar = false;
                                        }
                                    }
                                    if (debeTrasladar) {
                                        $card.addClass(
                                            'animar-traslado'
                                        ); // Solo trasladar si el rol del usuario destino NO es Gestor
                                        setTimeout(function() {
                                            $card.removeClass('animar-traslado');
                                            $('#columna-resueltas').append($card);
                                            $card.addClass('animar-llegada');
                                            setTimeout(function() {
                                                $card.removeClass('animar-llegada');
                                            }, 500);
                                            $card.css('border-left-color', '#28a745');
                                            $card.find('.solicitud-estado').text(
                                                'Estado: Resuelta');
                                            $card.find('.solicitud-estado').css({
                                                'color': '#28a745',
                                                'font-size': '11px',
                                                'margin-top': '5px'
                                            });
                                            actualizarContadores();
                                        }, 500);
                                    } else {
                                        $card.css('border-left-color',
                                            '#28a745'
                                        ); // Para rol Gestor, solo actualizar el estado visual sin mover la tarjeta
                                        $card.find('.solicitud-estado').text('Estado: Resuelta');
                                        $card.find('.solicitud-estado').css({
                                            'color': '#28a745',
                                            'font-size': '11px',
                                            'margin-top': '5px'
                                        });
                                    }
                                }
                            }
                            if (item.recepciones && item.recepciones.length > 0) {
                                let $usersContainer = $card.find('.users-container');
                                if ($usersContainer.length > 0) {
                                    let usersHtml = '';
                                    item.recepciones.forEach(function(recepcion) {
                                        if (recepcion.usuarioDestino) {
                                            let estadoColor =
                                                'rgb(170, 95, 34)'; // Color por defecto // Determinar colores basados en el estado de la tarjeta
                                            let badgeColor =
                                                'badge-secondary'; // Badge por defecto
                                            if (item.estado_id == 3) { // Resuelta
                                                badgeColor = 'badge-success';
                                            } else if (item.estado_id == 2) { // En progreso
                                                badgeColor = 'badge-primary';
                                            } else if (item.estado_id == 1) { // Recibida
                                                badgeColor = 'badge-secondary';
                                            }
                                            let userHtml =
                                                '<div style="margin: 0;" data-toggle="popover" ' +
                                                'data-title="' + (recepcion.usuarioDestino
                                                    .name || 'Sin asignar') + '" ' +
                                                'data-content="<span class=\'badge badge-pill ' +
                                                badgeColor + '\'>' + (recepcion.role ? recepcion
                                                    .role.name : 'Sin rol') + '</span> ' +
                                                '<span class=\'badge badge-pill ' + badgeColor +
                                                '\'>' + (recepcion.area ? recepcion.area.area :
                                                    'Sin área') + '</span>" ' +
                                                'data-trigger="hover" data-placement="top">';

                                            if (recepcion.usuarioDestino.profile_photo_url) {
                                                userHtml += '<img src="' + recepcion
                                                    .usuarioDestino.profile_photo_url +
                                                    '" alt="Usuario" class="avatar" ' +
                                                    'style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">';
                                            } else {
                                                userHtml += '<div class="avatar" ' +
                                                    'style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">' +
                                                    (recepcion.usuarioDestino.name ? recepcion
                                                        .usuarioDestino.name[0] : '?') +
                                                    '</div>';
                                            }
                                            userHtml += '</div>';
                                            usersHtml += userHtml;
                                        }
                                    });
                                    $usersContainer.find('[data-toggle="popover"]').popover(
                                        'dispose'); // Destruir popovers existentes antes de actualizar
                                    $usersContainer.html(usersHtml);
                                    $usersContainer.find('[data-toggle="popover"]')
                                        .popover({ // Reinicializar popovers para los nuevos elementos
                                            html: true,
                                            container: 'body',
                                            trigger: 'hover'
                                        });
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    // Evitar spam de errores en consola
                    if (xhr.status !== 0) { // Solo log si no es error de red
                        console.error('Error al consultar avances:', status);
                    }
                }
            });
        }
        function limpiarPopovers() { // Función para limpiar popovers abiertos
            $('[data-toggle="popover"]').popover('hide');
        }
        //FUNCIONES PARA LA CARGA INICIAL DE LAS TARJETAS
        function cargarTarjetasIniciales(tarjetas) {
            if (tarjetas.recibidas && tarjetas.recibidas.length > 0) { // Cargar tarjetas recibidas
                tarjetas.recibidas.forEach(function(tarjeta) {
                    let html = generarTarjetaSolicitud(tarjeta, false, 'recibidas');
                    $('#columna-recibidas').append(html);
                });
            }
            if (tarjetas.progreso && tarjetas.progreso.length > 0) { // Cargar tarjetas en progreso
                tarjetas.progreso.forEach(function(tarjeta) {
                    let html = generarTarjetaSolicitud(tarjeta, false, 'progreso');
                    $('#columna-progreso').append(html);
                });
            }
            if (tarjetas.resueltas && tarjetas.resueltas.length > 0) { // Cargar tarjetas resueltas
                tarjetas.resueltas.forEach(function(tarjeta) {
                    let html = generarTarjetaSolicitud(tarjeta, false, 'resueltas');
                    $('#columna-resueltas').append(html);
                });
            }
            actualizarContadores(); // Actualizar contadores y mensajes
            actualizarMensajeColumnaVacia();
            $('[data-toggle="popover"]').popover({ // Inicializar popovers para las tarjetas cargadas
                html: true,
                container: 'body',
                trigger: 'hover'
            });
        }
        function generarTarjetaSolicitud(tarjeta, animar = false, tipo = 'recibidas') {
            const titulo = tarjeta.titulo && tarjeta.detalle ?
                `${tarjeta.titulo} - ${tarjeta.detalle}` :
                tarjeta.titulo || tarjeta.detalle || 'Sin título';
            let borderColor, estadoColor, badgeColor; // Configurar colores según el tipo de tarjeta
            switch (tipo) {
                case 'recibidas':
                    borderColor = 'badge-secondary'; // Usar nombre estándar
                    estadoColor = '#2c3e50'; // Color que coincide con el header del tablero
                    badgeColor = 'badge-secondary';
                    break;
                case 'progreso':
                    borderColor = 'badge-primary'; // Usar nombre estándar
                    estadoColor = '#17a2b8';
                    badgeColor = 'badge-primary';
                    break;
                case 'resueltas':
                    borderColor = 'badge-success'; // Usar nombre estándar
                    estadoColor = '#28a745';
                    badgeColor = 'badge-success';
                    break;
                default:
                    borderColor = 'badge-secondary';
                    estadoColor = '#2c3e50';
                    badgeColor = 'badge-secondary';
            }
            let usersHtml = ''; // Generar HTML de usuarios
            if (tarjeta.users && tarjeta.users.length > 0) {
                tarjeta.users.forEach(function(user) {
                    const avatar = user.profile_photo_url ?
                        `<img src="${user.profile_photo_url}" alt="Usuario" class="avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">` :
                        `<div class="avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">${user.name ? user.name[0] : '?'}</div>`;

                    usersHtml += `
                        <div style="margin: 0;" data-toggle="popover" 
                            data-title="${user.name || 'Sin asignar'}" 
                            data-content="<span class='badge badge-pill ${badgeColor}'>${user.recepcion_role_name || 'Sin rol'}</span> 
                            <span class='badge badge-pill ${badgeColor}'>${user.area_name || 'Sin área'}</span>"
                            data-trigger="hover"
                            data-placement="top">
                            ${avatar}
                        </div>
                    `;
                });
            }
            return `
                <div class="solicitud-card ${animar ? 'animar-llegada' : ''} border-${borderColor}" 
                     data-id="${tarjeta.recepcion_id}"
                     data-atencion-id="${tarjeta.atencion_id}"
                     data-estado-id="${tarjeta.estado_id}"
                     data-fecha="${tarjeta.created_at}">
                    
                    <div class="solicitud-titulo">${titulo}</div>
                    
                    <div class="row">
                        <div class="solicitud-id text-center">
                            <small style="font-size: 0.7rem;">${tarjeta.solicitud_id_ripped}</small>
                        </div>
                        <div class="fecha-solicitud">${tarjeta.fecha_relativa}</div>
                    </div>
                    
                    <div class="solicitud-estado" style="font-size: 11px; color: ${estadoColor}; margin-top: 5px;">
                        Estado: ${tarjeta.estado}
                    </div>
                    
                    <div class="progress-divider" data-atencion-id="${tarjeta.atencion_id}" data-avance="${tarjeta.porcentaje_progreso}"></div>
                    
                    <div class="users-container" style="display: flex; align-items: center; justify-content: end; margin-top: 8px; padding-top: 6px;">
                        ${usersHtml}
                    </div>
                </div>
            `;
        }
        function obtenerRecepcionIdsExistentes() {
            let ids = [];
            $('.solicitud-card').each(function() {
                let recepcionId = $(this).data('id');
                if (recepcionId && recepcionId !== 'null' && recepcionId !== 'undefined') {
                    ids.push(recepcionId);
                }
            });
            return ids;
        }
        function cargarNuevasRecibidas() {
            let cantidadTarjetas = $('#columna-recibidas .solicitud-card')
                .length; // Evitar consulta si hay 3 o más tarjetas (parametrizable posteriormente)
            if (cantidadTarjetas >= 3) {
                return; // Salir de la función sin hacer consulta
            }
            let recepcionIdsExistentes = obtenerRecepcionIdsExistentes();
            let data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                recepcion_ids: recepcionIdsExistentes
            };
            $.post({
                url: '{{ route('recepcion.nuevas-recibidas') }}',
                data: data,
                success: function(nuevas) {
                    if (nuevas.length > 0) {
                        let tarjetasAgregadas = 0;
                        nuevas.forEach(function(tarjeta) {
                            let tarjetaExistente = $(
                                `#columna-recibidas .solicitud-card[data-id="${tarjeta.recepcion_id}"]`
                            ); // Verificar si la tarjeta ya existe
                            if (tarjetaExistente.length === 0) {
                                let html = generarTarjetaSolicitud(tarjeta, true,
                                    'recibidas'); // Solo agregar si no existe
                                let $nueva = $(html);
                                $('#columna-recibidas').prepend($nueva);
                                updateProgressByPercentage(tarjeta.atencion_id, tarjeta
                                    .porcentaje_progreso);
                                setTimeout(function() {
                                    $nueva.removeClass('animar-llegada');
                                }, 500);
                                tarjetasAgregadas++;
                            }
                        });
                        if (tarjetasAgregadas > 0) { // Solo actualizar contadores si se agregaron tarjetas
                            actualizarContadores();

                            // Inicializar popovers para las nuevas tarjetas
                            $('[data-toggle="popover"]').popover({
                                html: true,
                                container: 'body',
                                trigger: 'hover'
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status !==
                        0) { // Solo log si no es error de red // Evitar spam de errores en consola
                        console.error('Error cargando nuevas recibidas:', status);
                    }
                }
            });
        }
        //COMANDOS PARA CARGAR Y ACTUALIZAR LOS ELEMENTOS DE LA PÁGINA
        $(document).ready(function() {
            const tarjetasIniciales = { // Datos iniciales de las tarjetas
                recibidas: @json($recibidas),
                progreso: @json($progreso),
                resueltas: @json($resueltas)
            };
            cargarTarjetasIniciales(tarjetasIniciales); // Cargar tarjetas iniciales
            setTimeout(function() {
                $('.progress-divider').each(function() {
                    let atencionId = $(this).data('atencion-id');
                    let avance = $(this).data('avance');
                    if (atencionId && avance !== undefined) {
                        updateProgressByPercentage(atencionId, avance);
                    }
                });
            }, 100);
            initKanban();
            setInterval(consultarAvancesTablero, 30000); // 30 segundos
            setInterval(cargarNuevasRecibidas, 30000); // 30 segundos
            //setInterval(limpiarPopovers, 30000); // 30 segundos
        });
</script>
@endsection