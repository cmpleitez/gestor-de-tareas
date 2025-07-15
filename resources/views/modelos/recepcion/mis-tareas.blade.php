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
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .solicitud-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
        #heading5[aria-expanded="true"] .accordion-arrow { transform: rotate(180deg); }
        
        /* Cards con altura uniforme */
        .accordion .row { display: flex; align-items: stretch; }
        .accordion .col-md-3 { display: flex; }
        .accordion .card { flex: 1; display: flex; flex-direction: column; }
        .accordion .card-header { flex-shrink: 0; }
        .accordion .card-body { flex: 1; }
        
        /* Diseño de selección de departamentos */
        .dept-selector {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .dept-selector:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        .dept-selector.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            box-shadow: 0 4px 16px rgba(0,123,255,0.2);
        }
        .dept-selector .dept-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dept-selector .dept-body {
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dept-selector .dept-info {
            flex: 1;
        }
        .dept-selector .dept-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        .dept-selector .dept-desc {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .dept-selector .radio-indicator {
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
        .dept-selector.selected .radio-indicator {
            border-color: #007bff;
            background: #007bff;
        }
        .dept-selector.selected .radio-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* Utilidades tipográficas */
        .font-weight-500 { font-weight: 500 !important; }
        .font-weight-600 { font-weight: 600 !important; }
        .letter-spacing-1 { letter-spacing: 0.5px; }
    </style>
@endsection

@section('contenedor')
{{-- ITEMS DESTINATARIOS PARA CADA ROL --}}
<div class="row">
    <div class="col-12">
        <div class="accordion" id="accordionWrapa2">
            <div class="card collapse-header shadow-sm border-0 overflow-hidden">
                <div id="heading5" class="card-header" data-toggle="collapse" data-target="#accordion5" aria-expanded="false"
                    aria-controls="accordion5" role="tablist" style="border: none; margin: 0; cursor: pointer; transition: all 0.3s ease;">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <div class="mr-2">
                                <i class="bx bx-target-lock text-white" style="font-size: 1.1rem;"></i>
                            </div>
                            <div class="d-flex flex-column align-items-start justify-content-center">
                                <h6 class="mb-0 text-white font-weight-500" style="font-size: 1rem; letter-spacing: 0.3px;">
                                    @if(auth()->user()->hasRole('Recepcionista'))
                                        <span class="font-weight-600">{{ auth()->user()->area->area }}</span>
                                    @elseif(auth()->user()->hasRole('Supervisor'))
                                        <span class="font-weight-600">{{ auth()->user()->equipos()->first()->equipo }}</span>
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
                            <i class="bx bx-chevron-down text-white accordion-arrow" style="font-size: 1rem; transition: transform 0.3s ease;"></i>
                        </div>
                    </div>
                </div>
                <div id="accordion5" role="tabpanel" data-parent="#accordionWrapa2" aria-labelledby="heading5" class="collapse">
                    <div class="card-content">
                        <div class="card-body" style="background: #f8f9fa; padding: 1.5rem;">
                            @if (auth()->user()->hasRole('Recepcionista') && isset($areas)) {{-- RECEPCIONISTA --}}
                                <div class="row" style="display: flex; align-items: stretch;">
                                    @foreach ($areas as $area)
                                        <div class="col-md-3">
                                            <div class="dept-selector {{ $area->id == auth()->user()->area_id ? 'selected' : '' }}" 
                                                onclick="selectDepartment('area_{{ $area->id }}')">
                                                <div class="dept-header">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bx bx-building mr-2"></i>
                                                        <span>Área</span>
                                                    </div>
                                                    <div class="radio-indicator"></div>
                                                </div>
                                                <div class="dept-body">
                                                    <div class="dept-info">
                                                        <div class="dept-name">{{ $area->area }}</div>
                                                        <div class="dept-desc">Departamento de trabajo</div>
                                                    </div>
                                                </div>
                                                <input type="radio" id="area_{{ $area->id }}" name="area_destino" 
                                                    value="{{ $area->id }}" 
                                                    {{ $area->id == auth()->user()->area_id ? 'checked' : '' }} 
                                                    style="display: none;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif (auth()->user()->hasRole('Supervisor') && isset($equipos)) {{-- SUPERVISOR --}}
                                <div class="row" style="display: flex; align-items: stretch;">
                                    @foreach ($equipos as $equipo)
                                        <div class="col-md-3">
                                            <div class="dept-selector {{ $equipo->id == auth()->user()->equipos->first()->id ? 'selected' : '' }}" 
                                                onclick="selectDepartment('equipo_{{ $equipo->id }}')">
                                                <div class="dept-header">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bx bx-group mr-2"></i>
                                                        <span>Equipo</span>
                                                    </div>
                                                    <div class="radio-indicator"></div>
                                                </div>
                                                <div class="dept-body">
                                                    <div class="dept-info">
                                                        <div class="dept-name">{{ $equipo->equipo }}</div>
                                                        <div class="dept-desc">Grupo de trabajo</div>
                                                    </div>
                                                </div>
                                                <input type="radio" id="equipo_{{ $equipo->id }}" name="equipo_destino" 
                                                    value="{{ $equipo->id }}" 
                                                    {{ $equipo->id == auth()->user()->equipos->first()->id ? 'checked' : '' }} 
                                                    style="display: none;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif (auth()->user()->hasRole('Gestor') && isset($operadores)) {{-- GESTOR --}}
                                <div class="row" style="display: flex; align-items: stretch;">
                                    @foreach ($operadores as $operador)
                                        <div class="col-md-3">
                                            <div class="dept-selector {{ $operador->id == auth()->user()->id ? 'selected' : '' }}" 
                                                onclick="selectDepartment('operador_{{ $operador->id }}')">
                                                <div class="dept-header">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bx bx-user mr-2"></i>
                                                        <span>Operador</span>
                                                    </div>
                                                    <div class="radio-indicator"></div>
                                                </div>
                                                <div class="dept-body">
                                                    <div class="dept-info">
                                                        <div class="dept-name">{{ $operador->name }}</div>
                                                        <div class="dept-desc">Personal asignado</div>
                                                    </div>
                                                </div>
                                                <input type="radio" id="operador_{{ $operador->id }}" name="operador_destino" 
                                                    value="{{ $operador->id }}" 
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
<div class="row" style="display: flex; align-items: stretch;">
    <div class="col-md-4">
        <div class="card border-0 overflow-hidden">
            <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; margin: 0;">
                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <i class="bx bx-archive text-white" style="font-size: 0.9rem;"></i>
                    </div>
                    <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Recibidas</h6>
                    <span class="badge badge-light ml-auto" id="contador-recibidas">0</span>
                </div>
            </div>
            <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                <div id="columna-recibidas" class="sortable-column">
                    <div class="text-center text-muted py-4">
                        <i class="bx bx-loader-alt bx-spin text-primary" style="font-size: 1.5rem;"></i>
                        <div class="mt-2">Cargando solicitudes...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 overflow-hidden">
            <div class="card-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none; margin: 0;">
                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <i class="bx bx-time-five text-white" style="font-size: 0.9rem;"></i>
                    </div>
                    <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">En Progreso</h6>
                    <span class="badge badge-light ml-auto" id="contador-progreso">0</span>
                </div>
            </div>
            <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                <div id="columna-progreso" class="sortable-column">
                    <div class="text-center text-muted py-4">
                        <i class="bx bx-hourglass text-warning" style="font-size: 1.5rem;"></i>
                        <div class="mt-2">Sin tareas en progreso</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 overflow-hidden">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none; margin: 0;">
                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <i class="bx bx-check-circle text-white" style="font-size: 0.9rem;"></i>
                    </div>
                    <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Resueltas</h6>
                    <span class="badge badge-light ml-auto" id="contador-resueltas">0</span>
                </div>
            </div>
            <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                <div id="columna-resueltas" class="sortable-column">
                    <div class="text-center text-muted py-4">
                        <i class="bx bx-trophy text-success" style="font-size: 1.5rem;"></i>
                        <div class="mt-2">Sin tareas completadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- OVERLAY Y SIDEBAR KANVAN --}}
<div class="kanban-overlay"></div>
<div class="kanban-sidebar">
    <div class="card shadow-none">
        <div class="card-header d-flex justify-content-between align-items-center border-bottom px-2 py-1">
            <h3 class="card-title" id="sidebar-card-title">Detalle de Solicitud</h3>
            <button type="button" class="close close-icon">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="card-body" id="sidebar-card-body">
            <p>Selecciona una tarjeta para ver detalles...</p>
        </div>
    </div>
</div>
@endsection

@section('js')
    {{-- LIBRERIAS --}}
    <script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/jkanban/Sortable.min.js') }}"></script>
    {{-- LOGICA KANBAN --}}
    <script>
        let userRole = '';
        @if(auth()->user()->hasRole('Recepcionista'))
            userRole = 'Recepcionista';
        @elseif(auth()->user()->hasRole('Supervisor'))
            userRole = 'Supervisor';
        @elseif(auth()->user()->hasRole('Gestor'))
            userRole = 'Gestor';
        @elseif(auth()->user()->hasRole('Operador'))
            userRole = 'Operador';
        @endif
        // Función para seleccionar departamentos
        function selectDepartment(radioId) {
            // Desmarcar todos los selectores
            document.querySelectorAll('.dept-selector').forEach(selector => {
                selector.classList.remove('selected');
            });
            
            // Marcar el seleccionado
            const selectedElement = document.querySelector(`[onclick="selectDepartment('${radioId}')"]`);
            if (selectedElement) {
                selectedElement.classList.add('selected');
            }
            
            // Marcar el radio button
            const radio = document.getElementById(radioId);
            if (radio) {
                radio.checked = true;
                // Disparar el evento change para actualizar el título
                $(radio).trigger('change');
            }
        }

        $(document).ready(function() {
            // LECTURA DE DATOS
            function cargarTarjetas() {
                $('#columna-recibidas').html(
                    '<div class="text-center text-muted py-4"><i class="bx bx-loader-alt bx-spin text-primary" style="font-size: 1.5rem;"></i><div class="mt-2">Cargando solicitudes...</div></div>'
                );
                $.ajax({
                    url: '{{ route('recepcion.solicitudes') }}',
                    type: 'GET',
                    timeout: 3000,
                    cache: false,
                    success: function(response) {
                        const recepciones = response.recepciones || [];
                        ubicarTarjetas(recepciones);
                    },
                    error: function(xhr, status, error) {
                        $('#columna-recibidas').html(
                            '<div class="text-center text-danger py-4"><i class="bx bx-error-circle" style="font-size: 1.5rem;"></i><div class="mt-2">Error al cargar solicitudes</div></div>'
                        );
                    }
                });
            }
            function ubicarTarjetas(recepciones) {
                $('#columna-recibidas, #columna-progreso, #columna-resueltas').empty(); // Limpiar todos los tableros
                
                let contadores = { // Contadores para verificar si hay tarjetas en cada columna
                    recibidas: 0,
                    progreso: 0,
                    resueltas: 0
                };
                recepciones.forEach(function(recepcion, index) { // Distribuir las tarjetas en los tableros según su estado
                    const estadoId = recepcion.estado_id || 1;
                    const estadoNombre = recepcion.estado || 'Recibida';
                    let colorBorde = '#007bff';
                    let columnaDestino = 'columna-recibidas';
                    if (estadoId === 1) {
                        colorBorde = '#ffc107';
                        columnaDestino = 'columna-recibidas';
                        contadores.recibidas++;
                    } else if (estadoId === 2) {
                        colorBorde = '#17a2b8';
                        columnaDestino = 'columna-progreso';
                        contadores.progreso++;
                    } else if (estadoId === 3) {
                        colorBorde = '#28a745';
                        columnaDestino = 'columna-resueltas';
                        contadores.resueltas++;
                    }
                    const tarjetaHtml = `
                    <div class="solicitud-card" data-id="${recepcion.recepcion_id}" data-estado-id="${recepcion.estado_id}" style="border-left-color: ${colorBorde};">
                        <div class="solicitud-titulo">${recepcion.titulo || recepcion.detalle || 'Sin título'}</div>
                        <div class="solicitud-id">ID: ${recepcion.atencion_id}</div>
                        <div class="solicitud-estado" style="font-size: 11px; color: ${colorBorde}; margin-top: 5px;">
                            Estado: ${estadoNombre} (${recepcion.recepcion_id})
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px; border-top: 1px solid #f0f0f0;">
                            <div style="display: flex; flex-direction: column; justify-content: center; height: 32px; flex: 1;">
                                <div style="text-align: right; font-size: 10px; color: #6c757d; line-height: 1.2; margin-bottom: 1px;">
                                    ${recepcion.user_name}
                                </div>
                                <div style="text-align: right; background:rgb(239, 242, 247); padding: 1px 6px; border-radius: 3px; font-size: 9px; color: #495057; font-weight: 500; display: inline-block; margin-left: auto;">
                                    ${recepcion.role_name + ' del área ' + recepcion.area}
                                </div>
                            </div>
                            <div style="margin-left: 8px;">
                                ${recepcion.user_foto ? 
                                    `<img src="${recepcion.user_foto}" alt="Usuario" class="avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">` 
                                    : 
                                    `<div class="avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">?</div>`
                                }
                            </div>
                        </div>
                    </div>`;
                    $(`#${columnaDestino}`).append(tarjetaHtml);
                });
                $('#contador-recibidas').text(contadores.recibidas); // Actualizar contadores
                $('#contador-progreso').text(contadores.progreso);
                $('#contador-resueltas').text(contadores.resueltas);
                
                if (contadores.recibidas === 0) { // Mostrar mensajes si alguna columna quedó vacía
                    $('#columna-recibidas').html(
                        '<div class="text-center text-muted py-4"><i class="bx bx-archive text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin solicitudes recibidas</div></div>');
                }
                if (contadores.progreso === 0) {
                    $('#columna-progreso').html(
                        '<div class="text-center text-muted py-4"><i class="bx bx-time-five text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin tareas en progreso</div></div>');
                }
                if (contadores.resueltas === 0) {
                    $('#columna-resueltas').html(
                        '<div class="text-center text-muted py-4"><i class="bx bx-check-circle text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin tareas completadas</div></div>');
                }
                initKanban();
            }
            cargarTarjetas(); //Cargar las solicitudes desde el servidor
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
                            timer: 2000
                        });
                        $(evt.from).append(evt.item);
                        return;
                    }
                    url = '{{ route("recepcion.derivar", ["recepcion_id" => ":id", "area_id" => ":area"]) }}'
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
                            timer: 2000
                        });
                        $(evt.from).append(evt.item);
                        return;
                    }
                    url = '{{ route("recepcion.asignar", ["recepcion_id" => ":id", "equipo_id" => ":equipo"]) }}'
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
                            timer: 2000
                        });
                        $(evt.from).append(evt.item);
                        return;
                    }
                    url = '{{ route("recepcion.delegar", ["recepcion_id" => ":id", "user_id" => ":user"]) }}'
                        .replace(':id', solicitudId)
                        .replace(':user', selectedValue);
                } else if (userRole === 'Operador') { //OPERADOR
                    alert('entro');


                    url = '{{ route("recepcion.iniciar-tareas", ["recepcion_id" => ":id"]) }}'
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
                                title: `Solicitud #${String(solicitudId).slice(-3)} "${tituloTarjeta}" 👉🏻 ${nombreEstado}`,
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
                                confirmButtonClass: 'btn btn-primary',
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
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false
                        });
                        $(evt.from).append(evt.item); // Revertir la tarjeta a su posición original
                    }
                });
            }
            
            //MOSTRAR DETALLE EN SIDEBAR KANBAN
            $(document).on('click', '.solicitud-card', function() {
                const $card = $(this);
                const titulo = $card.find('.solicitud-titulo').text().trim();
                const atencion = $card.find('.solicitud-id').text().trim();
                const recepcionId = $card.data('id');

                // Rellenar la información en el sidebar
                $('#sidebar-card-title').text(titulo);
                $('#sidebar-card-body').html('<p>' + atencion + '</p>');
                
                // Cargar y dibujar las tareas
                cargarTareas(recepcionId);

                // Mostrar overlay y sidebar
                $('.kanban-overlay').addClass('show');
                $('.kanban-sidebar').addClass('show');
            });

            // Función para cargar y dibujar las tareas
            function cargarTareas(recepcionId) {
                $.ajax({
                    url: '{{ route("recepcion.tareas", ["recepcion_id" => ":id"]) }}'.replace(':id', recepcionId),
                    type: 'GET',
                    cache: false,
                    success: function(response) {
                        const tareas = response.tareas || [];
                        dibujarTareas(tareas);
                    },
                    error: function(xhr, status, error) {
                        $('#sidebar-card-body').append('<div class="text-center text-muted py-3"><i class="bx bx-error-circle text-danger"></i><div class="mt-2">Error al cargar tareas</div></div>');
                    }
                });
            }

            // Función para dibujar las tareas en el sidebar
            function dibujarTareas(tareas) {
                if (tareas.length === 0) {
                    $('#sidebar-card-body').append('<div class="text-center text-muted py-3"><i class="bx bx-task text-muted"></i><div class="mt-2">Sin tareas asignadas</div></div>');
                    return;
                }

                let tareasHtml = '<div class="mt-3"><h6 class="font-weight-600 mb-2">Tareas de la solicitud:</h6>';
                
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
                        <div class="dept-selector" style="margin-bottom: 8px; border: 1px solid #e3e6f0;">
                            <div class="dept-header" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); color: #495057; padding: 8px 12px;">
                                <div class="d-flex align-items-center">
                                    <i class="bx ${estadoIcon} mr-2" style="color: ${estadoColor}; font-size: 0.9rem;"></i>
                                    <span style="font-size: 0.8rem; font-weight: 500;">Tarea</span>
                                </div>
                                <div style="font-size: 0.7rem; color: ${estadoColor}; font-weight: 600;">${tarea.estado}</div>
                            </div>
                            <div class="dept-body" style="padding: 10px 12px;">
                                <div class="dept-info">
                                    <div class="dept-name" style="font-size: 0.85rem; margin-bottom: 2px;">${tarea.tarea}</div>
                                    <div class="dept-desc" style="font-size: 0.7rem;">ID: ${tarea.actividad_id}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                tareasHtml += '</div>';
                $('#sidebar-card-body').append(tareasHtml);
            }

            //Cerrar sidebar al hacer clic en overlay o en el icono de cierre
            $(document).on('click', '.kanban-overlay, .kanban-sidebar .close-icon', function() {
                $('.kanban-overlay').removeClass('show');
                $('.kanban-sidebar').removeClass('show');
            });

            //ACTUALIZACIÓN DINÁMICA DEL TÍTULO DEL ACORDEÓN
            $(document).on('change', 'input[name="area_destino"]', function() {
                const areaId = $(this).val();
                const areaNombre = $(this).closest('.dept-selector').find('.dept-name').text().trim();
                $('#heading5 h6 .font-weight-600').text(areaNombre);
                $('#accordion5').collapse('hide');
            });
            $(document).on('change', 'input[name="equipo_destino"]', function() {
                const equipoId = $(this).val();
                const equipoNombre = $(this).closest('.dept-selector').find('.dept-name').text().trim();
                $('#heading5 h6 .font-weight-600').text(equipoNombre);
                $('#accordion5').collapse('hide');
            });
            $(document).on('change', 'input[name="operador_destino"]', function() {
                const operadorId = $(this).val();
                const operadorNombre = $(this).closest('.dept-selector').find('.dept-name').text().trim();
                $('#heading5 h6 .font-weight-600').text(operadorNombre);
                $('#accordion5').collapse('hide');
            });
        });
    </script>
@endsection
