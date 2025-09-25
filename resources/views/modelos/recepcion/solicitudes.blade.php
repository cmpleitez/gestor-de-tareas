@extends('dashboard')

@section('css')
    <link href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/pages/app-kanban.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/solicitudes.css') }}" rel="stylesheet">
@endsection

@section('contenedor')
    {{-- EQUIPOS DE TRABAJO DESTINO --}}
    @can('asignar')
        <div class="row">
            <div class="col-12">
                @if (optional(auth()->user()->mainRole)->name != 'Operador')
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
                                                @if (optional(auth()->user()->mainRole)->name == 'Receptor')
                                                    <span
                                                        class="font-weight-600">{{ auth()->user()->equipos()->first()->equipo }}</span>
                                                @endif
                                            </h6>
                                            <small class="text-white-50" style="font-size: 0.8rem;">
                                                Selecciona el equipo de trabajo destino para impulsar las solicitudes
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
                                        @if (optional(auth()->user()->mainRole)->name == 'Receptor' && isset($equipos))
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
                                                            <input type="radio" id="equipo_{{ $equipo->id }}"
                                                                name="equipo_destino" value="{{ $equipo->id }}"
                                                                {{ $equipo->id == auth()->user()->equipos->first()->id ? 'checked' : '' }}
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
    @endcan
    {{-- TABLEROS KANBAN --}}
    <div class="row kanban-container" style="display: flex; align-items: stretch;">
        <div class="col-md-4"> {{-- Recibidas --}}
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-archive text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Recibidas</h6>
                        <div class="ml-auto d-flex align-items-center">

                            <span class="badge badge-white text-dark"
                                id="contador-recibidas">{{ count($recibidas) }}</span>
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
        <div class="col-md-4"> {{-- En Progreso --}}
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-time-five text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">En Progreso</h6>
                        <span class="badge badge-white ml-auto text-dark"
                            id="contador-progreso">{{ count($progreso) }}</span>
                    </div>
                </div>
                <div class="card-body kanban-columna" style="background: #f8f9fa; padding: 1rem;">
                    <div id="columna-progreso" class="sortable-column">
                        {{-- Las tarjetas se dibujarán con JavaScript --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4"> {{-- Resueltas --}}
            <div class="card border-0 overflow-hidden">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none; margin: 0;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <i class="bx bx-check-circle text-white" style="font-size: 0.9rem;"></i>
                        </div>
                        <h6 class="mb-0 text-white font-weight-600" style="font-size: 0.9rem;">Resueltas</h6>
                        <span class="badge badge-white ml-auto text-dark"
                            id="contador-resueltas">{{ count($resueltas) }}</span>
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
    <div class="kanban-overlay"></div> {{-- Overlay Kanban --}}
    <div class="kanban-sidebar">
        <div class="d-flex justify-content-between align-items-center border-bottom px-1"
            style="background: linear-gradient(156deg, #221627 0%, #4e2a5d 100%); border: none; margin: 0; padding: 0.75rem 1rem; min-height: 52px;">
            <h4 id="sidebar-card-title" class="text-white mb-0">Titulo</h4>
            <button type="button" class="close close-icon">
                <i class="bx bx-x text-white"></i>
            </button>
        </div>
        <div id="sidebar-card-body">
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/jkanban/Sortable.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/jkanban/jkanban.min.js') }}"></script>
    <script>
        //SELECCIONANDO EL ITEM DESTINATARIO
        let userRole = @json(optional(auth()->user()->mainRole)->name) || '';

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
            @can('asignar')
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
                        onStart: function(evt) {
                            $('.text-center.text-muted.py-4')
                                .remove(); // Remover mensajes de columna vacía al iniciar drag
                        },
                        onEnd: function(
                            evt
                        ) {
                            @can('asignar') // Impulso manual hacia "En progreso"
                                const solicitudId = evt.item.dataset.recepcionId;
                                const columnaOrigen = evt.from.id;
                                const columnaDestino = evt.to.id;
                                if (columnaOrigen !== columnaDestino) {
                                    if (columnaOrigen !== 'columna-recibidas' || columnaDestino !==
                                        'columna-progreso') {
                                        toastr.error('Movimiento no disponible');
                                        $(evt.from).append(evt
                                            .item); // Revertir la tarjeta a su posición original
                                        actualizarMensajeColumnaVacia
                                            (); // Restaurar mensajes si se cancela el movimiento
                                        return;
                                    }
                                    const $colDestino = $('#' +
                                        columnaDestino
                                    ); // Ordenar inmediatamente la columna destino después del movimiento visual
                                    const itemsDestino = $colDestino.children('.solicitud-card').get();
                                    itemsDestino.sort(function(a, b) {
                                        return parseInt(a.dataset.atencionId || '0', 10) -
                                            parseInt(b.dataset.atencionId || '0', 10);
                                    });
                                    itemsDestino.forEach(function(el) {
                                        $colDestino.append(el);
                                    });
                                    updatePosition(solicitudId, columnaDestino, evt);
                                } else {
                                    actualizarMensajeColumnaVacia
                                        (); // Si no hay movimiento, restaurar mensajes
                                }
                                actualizarMensajeColumnaVacia();
                            @else
                                toastr.error('No tienes permiso para realizar esta acción');
                                $(evt.from).append(evt
                                    .item); // Revertir la tarjeta a su posición original
                                // Restaurar mensajes si no hay permisos
                                actualizarMensajeColumnaVacia();
                            @endcan
                        }
                    });
                });
            @else
                // Para usuarios sin permisos de asignar, solo inicializar las columnas sin drag & drop
                const columnas = ['columna-recibidas', 'columna-progreso', 'columna-resueltas'];
                columnas.forEach(function(columnaId) {
                    const elemento = document.getElementById(columnaId);
                    if (!elemento) return;
                    // Solo crear columnas vacías sin funcionalidad de drag & drop
                });
            @endcan
        }
        //FUNCIONES PARA ORDENAMIENTO DE LAS TARJETAS
        function ordenarColumna(columnaId) {
            const $col = $('#' + columnaId);
            const items = $col.children('.solicitud-card').get();
            if (items.length > 0) {
                items.sort(function(a, b) {
                    return parseInt(a.dataset.atencionId || '0', 10) - parseInt(b.dataset.atencionId || '0', 10);
                });
                items.forEach(function(el) {
                    $col.append(el);
                });
            }
        }

        function ordenarColumnas() {
            const columnas = ['columna-recibidas', 'columna-progreso', 'columna-resueltas'];
            columnas.forEach(function(columnaId) {
                ordenarColumna(columnaId);
            });
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
            ordenarColumnas(); // Ordenar todas las columnas después de la carga inicial
            actualizarContadores(); // Actualizar contadores y mensajes
            actualizarMensajeColumnaVacia();
            inicializarPopovers(); // Inicializar popovers para las tarjetas cargadas
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
            let usersHtml = generarHtmlUsuarios(tarjeta.users, tarjeta.estado_id,
                tipo); // Generar HTML de usuarios usando la función auxiliar estándar
            return `
                <div class="solicitud-card ${animar ? 'animar-llegada' : ''} border-${borderColor}" 
                data-recepcion-id="${tarjeta.recepcion_id}"
                data-atencion-id="${tarjeta.atencion_id}"
                data-recepcion-estado-id="${tarjeta.estado_id}"
                data-fecha="${tarjeta.created_at}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="solicitud-titulo flex-grow-1">${titulo}</div>
                    <div class="text-right ml-2">
                        <div style="font-size: 0.9rem; font-weight: 600;">${tarjeta.atencion_id_ripped}</div>
                        <div style="font-size: 0.6rem; color: #6c757d;">${tarjeta.fecha_relativa}</div>
                    </div>
                </div>
                <div class="solicitud-estado" style="font-size: 11px; color: ${estadoColor}; margin-top: 5px;">
                    ${tarjeta.traza}
                </div>
                <div class="progress-divider" data-atencion-id="${tarjeta.atencion_id}" data-avance="${tarjeta.porcentaje_progreso}"></div>
                <div class="users-container" style="display: flex; align-items: center; justify-content: end; margin-top: 8px; padding-top: 6px;">
                    ${usersHtml}
                </div>
            </div>
        `;
        }

        function obtenerAtencionIdsExistentes() {
            let ids = [];
            $('#columna-recibidas .solicitud-card').each(function() { // Solo obtener atencion_ids de la columna recibidas
                let atencionId = $(this).data('atencion-id');
                if (atencionId && atencionId !== 'null' && atencionId !== 'undefined') {
                    ids.push(atencionId);
                }
            });
            return ids;
        }
        //INICIALIZAR POPOVERS GLOBALMENTE
        function inicializarPopovers(selector = '[data-toggle="popover"]') {
            $(selector).not('[data-popover-initialized]')
                .popover({ // Solo inicializar popovers que no estén ya inicializados
                    html: true,
                    container: 'body',
                    trigger: 'hover',
                    placement: 'top',
                    boundary: 'viewport',
                    zIndex: 9999,
                    offset: '0, 8px',
                    fallbackPlacements: ['bottom', 'left', 'right'],
                    delay: {
                        show: 300,
                        hide: 100
                    }
                }).attr('data-popover-initialized', 'true');
        }
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
            let url = null; //Seleccionando la ruta a la que se va a enviar la solicitud
            let selectedValue = null;
            if (userRole === 'Receptor') { //Asignar
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
                url = '{{ route('recepcion.asignar', ['recepcion' => ':recepcion_id', 'equipo' => ':equipo_id']) }}'
                    .replace(':recepcion_id', solicitudId)
                    .replace(':equipo_id', selectedValue);
            } else if (userRole === 'Operador') { //Iniciar tareas
                url = '{{ route('recepcion.iniciar-tareas', ['recepcion_id' => ':id']) }}'
                    .replace(':id', solicitudId);
            }
            if (!url) {
                $(evt.from).append(evt.item);
                return;
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
                        const tarjeta = $(`.solicitud-card[data-recepcion-id="${solicitudId}"]`);
                        const tituloTarjeta = tarjeta.find('.solicitud-titulo').text() || 'Sin título';
                        if (tarjeta.length > 0) {
                            // Actualizar estilos básicos de la tarjeta
                            tarjeta.removeClass(
                                'border-badge-secondary border-badge-primary border-badge-success border-badge-danger border-badge-warning'
                            );
                            tarjeta.addClass('border-' + colorBorde);
                            tarjeta.find('.solicitud-estado').text(response.traza || 'Recibida');
                            tarjeta.find('.solicitud-estado').css({
                                'color': estadoColor,
                                'font-size': '11px',
                                'margin-top': '5px'
                            });
                            tarjeta.attr('data-recepcion-estado-id', nuevoEstadoId);
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
                            // Actualizar color de la flecha según el nuevo estado
                            tarjeta.find('.fas.fa-arrow-right').css('color', estadoColor);
                        }
                        toastr.success(response.message);
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
                        timer: 60000,
                        confirmButtonClass: 'btn btn-danger',
                        buttonsStyling: false
                    });
                    $(evt.from).append(evt.item); // Revertir la tarjeta a su posición original
                    actualizarMensajeColumnaVacia(); // Actualizar mensaje de columna vacía
                }
            });
        }

        function actualizarMensajeColumnaVacia() { //Mostrar u ocultar mensaje de columna vacía
            const columnas = [{
                    id: 'columna-recibidas',
                    mensaje: '<div class="text-center text-muted py-4" style="pointer-events: none; user-select: none;"><i class="bx bx-archive text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin solicitudes recibidas</div></div>'
                },
                {
                    id: 'columna-progreso',
                    mensaje: '<div class="text-center text-muted py-4" style="pointer-events: none; user-select: none;"><i class="bx bx-time-five text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin tareas en progreso</div></div>'
                },
                {
                    id: 'columna-resueltas',
                    mensaje: '<div class="text-center text-muted py-4" style="pointer-events: none; user-select: none;"><i class="bx bx-check-circle text-muted" style="font-size: 1.5rem;"></i><div class="mt-2">Sin tareas completadas</div></div>'
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
        //MOSTRAR TAREAS EN SIDEBAR
        @can('asignar')
            @if (auth()->user()->mainRole->name === 'Operador')
                $(document).on('click', '.solicitud-card', function() {
                    const $card = $(this);
                    const titulo = $card.find('.solicitud-titulo').text().trim();
                    const atencionId = $card.data('atencion-id');
                    const recepcionId = $card.data('recepcion-id');
                    const atencionIdRipped = $card.find('.text-right div[style*="font-weight: 600"]').text().trim();
                    $('#sidebar-card-title').text(atencionIdRipped + ' - ' + titulo).css('font-size', '1rem');
                    $('#sidebar-card-body').empty();
                    cargarTareas(recepcionId);
                    $('.kanban-overlay').addClass('show');
                    $('.kanban-sidebar').addClass('show');
                    $('body').addClass('sidebar-open');
                    limpiarClasesDrag();
                });
            @endif

            function cargarTareas(recepcionId) {
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

            function dibujarTareas(tareas) {
                if (tareas.length === 0) {
                    $('#sidebar-card-body').append(
                        '<div class="text-center text-muted py-3"><i class="bx bx-task text-muted"></i><div class="mt-2">Sin tareas asignadas</div></div>'
                    );
                    return;
                }
                let tareasHtml = '<div><h6 class="font-weight-600 mb-2"></h6>';
                tareas.forEach(function(tarea) {
                    let esCompletada = tarea.estado_id == 3;
                    let taskId = 'task_' + tarea.actividad_id;
                    let htmlGenerado = `
                <div class="selectable-item ${esCompletada ? 'selected' : ''}" ${esCompletada ? 'style="pointer-events: none;"' : 'onclick="selectTask(\'' + taskId + '\')"'}">
                    <div class="checkbox-indicator" id="checkbox_${tarea.actividad_id}" ${esCompletada ? 'style="background: none; border: none;"' : ''}>
                        ${esCompletada ? '<i class="bx bx-check" style="color: #28a745; font-size: 2rem;"></i>' : ''}
                    </div>
                    <div class="item-body">
                        <div class="item-info">
                            <div class="item-name">${tarea.tarea}</div>
                            <div class="item-desc">T-${tarea.actividad_id_ripped}</div>
                        </div>
                    </div>
                    ${!esCompletada ? `<input type="checkbox" id="${taskId}" name="tarea_completada" value="${tarea.actividad_id}" style="display: none;">` : ''}
                </div>
                `;
                    tareasHtml += htmlGenerado;
                });
                tareasHtml += '</div>';
                $('#sidebar-card-body').append(tareasHtml);
            }

            function limpiarClasesDrag() {
                $('.solicitud-card').removeClass('dragging sortable-drag sortable-chosen sortable-ghost');
                $('.sortable-fallback').remove();
            }
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    limpiarClasesDrag();
                }
            });
        @endcan
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
                            // Actualizar la traza en la tarjeta
                            const tarjeta = $(`.solicitud-card[data-recepcion-id="${response.recepcion_id}"]`);
                            if (tarjeta.length > 0 && response.traza) {
                                tarjeta.find('.solicitud-estado').text(response.traza);
                            }

                            updateProgressByPercentage(response.atencion_id, response.progreso.porcentaje);
                            if (response.todas_resueltas && response
                                .solicitud_actualizada) { // Verificar si todas las tareas están resueltas
                                const tarjeta = $(
                                    `.solicitud-card[data-recepcion-id="${response.recepcion_id}"]`
                                ); // Mover la tarjeta al tablero de resueltas
                                if (tarjeta.length > 0) {
                                    // Actualizar estilos usando la función auxiliar
                                    actualizarEstilosTarjeta(tarjeta, 3);
                                    tarjeta.attr('data-recepcion-estado-id', 3);
                                    $('#columna-resueltas').append(tarjeta);
                                    actualizarContadores();
                                    Swal.fire({
                                        position: 'top-end',
                                        type: 'success',
                                        title: '¡Se completaron todas las tareas! Solicitud movida a Resueltas',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        confirmButtonClass: 'btn btn-primary',
                                        buttonsStyling: false
                                    });
                                }
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    type: 'success',
                                    title: 'Tarea ' + String(actividadId).slice(-4) +
                                        ' se reportó como ' + nuevoEstado,
                                    showConfirmButton: false,
                                    timer: 500,
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
                                timer: 60000,
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
                            timer: 60000,
                            confirmButtonClass: 'btn btn-danger',
                            buttonsStyling: false
                        });
                    }
                });
            }
        }
        //CERRAR SIDEBAR
        $(document).on('click', '.kanban-overlay, .kanban-sidebar .close-icon',
            function() {
                $('.kanban-overlay').removeClass('show');
                $('.kanban-sidebar').removeClass('show');
                $('body').removeClass('sidebar-open'); // Reactivar scroll de la página principal

                //VERIFICAR SI SE USA
                $('.solicitud-card').removeClass(
                    'dragging sortable-drag sortable-chosen sortable-ghost'
                ); // Limpiar clases de rotación y drag que puedan haber quedado
                $('.sortable-fallback').remove();
            }
        );
        //CERRAR EL ACCORDION
        $(document).on('change', 'input[name="equipo_destino"]', function() {
            const equipoId = $(this).val();
            const equipoNombre = $(this).closest('div').find('.item-name').text().trim();
            $('#heading5 h6 span.font-weight-600').text(equipoNombre);
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
        // GENERAR HTML DE PARTICIPANTES
        function generarHtmlUsuarios(users, estadoId, tipo = 'recibidas') {
            let usersHtml = '';
            if (users && users.length > 0) {
                const cliente = users.find(user => user.tipo ===
                    'origen'); // Separar cliente (origen) de otros participantes (destino)
                const participantes = users.filter(user => user.tipo === 'destino');

                function generarAvatar(user) { // Función para generar avatar
                    return user.profile_photo_url ?
                        `<img src="${user.profile_photo_url}" alt="Usuario" class="avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">` :
                        `<div class="avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #e9ecef; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">${user.name ? user.name[0] : '?'}</div>`;
                }
                let badgeColor = 'badge-secondary'; // Determinar colores basados en el estado
                let estadoColor = '#2c3e50';
                if (estadoId == 3) { // Resuelta
                    badgeColor = 'badge-success';
                    estadoColor = '#28a745';
                } else if (estadoId == 2) { // En progreso
                    badgeColor = 'badge-primary';
                    estadoColor = '#17a2b8';
                } else if (estadoId == 1) { // Recibida
                    badgeColor = 'badge-secondary';
                    estadoColor = '#2c3e50';
                }
                if (cliente) { // Mostrar cliente primero
                    usersHtml += `
                        <div style="margin: 0;" data-toggle="popover" 
                            data-title="${cliente.name || 'Cliente'}" 
                            data-content="<span class='badge badge-pill ${badgeColor}'>Cliente</span>"
                            data-trigger="hover"
                            data-placement="top">
                            ${generarAvatar(cliente)}
                        </div>
                    `;
                    if (participantes.length > 0) { // Agregar flecha si hay participantes
                        usersHtml += `
                            <div style="margin: 0 8px; display: flex; align-items: center; justify-content: center; width: 20px; height: 32px;">
                                <i class="fas fa-arrow-right" style="color: ${estadoColor}; font-size: 14px;"></i>
                            </div>
                        `;
                    }
                }
                participantes.forEach(function(user) { // Mostrar participantes
                    usersHtml += `
                        <div style="margin: 0;" data-toggle="popover" 
                            data-title="${user.name || 'Sin asignar'}" 
                            data-content="<span class='badge badge-pill ${badgeColor}'>${user.recepcion_role_name || 'Sin rol'}</span>"
                            data-trigger="hover"
                            data-placement="top">
                            ${generarAvatar(user)}
                        </div>
                    `;
                });
            }
            return usersHtml;
        }
        //FUNCIÓN AUXILIAR PARA ACTUALIZAR ESTILOS DE TARJETA
        function actualizarEstilosTarjeta($card, estadoId) {
            let color, borderClass, nombreEstado;
            switch (estadoId) {
                case 1: // Recibida
                    color = '#2c3e50';
                    borderClass = 'border-badge-secondary';
                    nombreEstado = 'Recibida';
                    break;
                case 2: // En progreso
                    color = '#17a2b8';
                    borderClass = 'border-badge-primary';
                    nombreEstado = 'En progreso';
                    break;
                case 3: // Resuelta
                    color = '#28a745';
                    borderClass = 'border-badge-success';
                    nombreEstado = 'Resuelta';
                    break;
                default:
                    color = '#2c3e50';
                    borderClass = 'border-badge-secondary';
                    nombreEstado = 'Recibida';
            }
            $card.css('border-left-color', color);
            $card.removeClass(
                'border-badge-secondary border-badge-primary border-badge-success border-badge-danger border-badge-warning'
            );
            $card.addClass(borderClass);
            $card.find('.solicitud-estado').text(nombreEstado);
            $card.find('.solicitud-estado').css({
                'color': color,
                'font-size': '11px',
                'margin-top': '5px'
            });
            $card.find('.fas.fa-arrow-right').css('color', color);
        }
        //ACTUALIZAR AVANCE
        function actualizarAvance() {
            let atencionIds = obtenerAtencionIdsTableros();
            if (atencionIds.length === 0) { // No hay tarjetas en los tableros
                return;
            }
            $.post({
                url: '{{ route('recepcion.consultar-avance') }}',
                data: {
                    atencion_ids: atencionIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(items) {
                    items.forEach(function(item) {
                        let $divider = $('.progress-divider[data-atencion-id="' + item.atencion_id +
                            '"]');
                        let $card = $('.solicitud-card[data-atencion-id="' + item.atencion_id + '"]');
                        if ($divider.length > 0 && $card.length > 0) {
                            let avanceFrontend = parseFloat($divider.attr('data-avance') || '0');
                            let avanceBackend = parseFloat(item.avance || '0');
                            if (avanceFrontend !== avanceBackend) {
                                $divider.attr('data-avance', avanceBackend);
                                updateProgressByPercentage(item.atencion_id, avanceBackend);
                            }
                            let estadoFrontend = parseInt($card.attr('data-recepcion-estado-id'), 10);
                            let estadoBackend = parseInt(item.estado_id, 10);
                            if (item.traza) {
                                $card.find('.solicitud-estado').text(item.traza);
                            }
                            if (estadoFrontend !== estadoBackend) {
                                $card.attr('data-recepcion-estado-id', estadoBackend);
                                let columnaDestino; // Determinar columna destino según el estado
                                switch (estadoBackend) {
                                    case 1: // Recibida
                                        columnaDestino = '#columna-recibidas';
                                        break;
                                    case 2: // En progreso
                                        columnaDestino = '#columna-progreso';
                                        break;
                                    case 3: // Resuelta
                                        columnaDestino = '#columna-resueltas';
                                        break;
                                    default:
                                        columnaDestino = '#columna-recibidas';
                                }
                                $card.addClass(
                                    'animar-traslado'); // Trasladar tarjeta al tablero correspondiente
                                setTimeout(function() {
                                    $card.removeClass('animar-traslado');
                                    $(columnaDestino).append($card);
                                    $card.addClass('animar-llegada');
                                    setTimeout(function() {
                                        $card.removeClass('animar-llegada');
                                    }, 500);
                                    actualizarEstilosTarjeta($card,
                                        estadoBackend
                                    ); // Actualizar estilos usando la función auxiliar
                                    actualizarContadores();
                                    ordenarColumna(columnaDestino.replace('#',
                                        '')); // Ordenar la columna después del traslado
                                }, 500);
                            }
                            if (item.recepciones && item.recepciones.length > 0) {
                                let $usersContainer = $card.find('.users-container');
                                if ($usersContainer.length > 0) {
                                    let usersHtml = generarHtmlUsuarios(item.recepciones, item
                                        .estado_id);
                                    $usersContainer.find('[data-toggle="popover"]').popover('dispose');
                                    $usersContainer.html(usersHtml);
                                    setTimeout(() => { // Esperar un momento antes de reinicializar
                                        inicializarPopovers($usersContainer.find(
                                            '[data-toggle="popover"]'));
                                    }, 50);
                                }
                            }
                        }
                    });
                    ordenarColumnas(); // Ordenar todas las columnas después de las actualizaciones
                },
                error: function(xhr, status, error) {
                    console.error('Error al consultar avances:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    if (xhr.status !== 0) {
                        console.error('Error al consultar avances:', status);
                    }
                }
            });
        }
        //CARGAR NUEVAS RECIBIDAS
        function cargarNuevasRecibidas() {
            let atencionIdsExistentes = obtenerAtencionIdsExistentes();
            let data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                atencion_ids: atencionIdsExistentes
            };
            $.post({
                url: '{{ route('recepcion.nuevas-recibidas') }}',
                data: data,
                success: function(nuevas) {
                    if (!Array.isArray(nuevas)) {
                        nuevas = Object.values(nuevas);
                    }
                    if (nuevas && nuevas.length > 0) {
                        let tarjetasAgregadas = 0;
                        nuevas.forEach(function(tarjeta) {
                            let html = generarTarjetaSolicitud(tarjeta, true, 'recibidas');
                            let $nueva = $(html);
                            $('#columna-recibidas').prepend($nueva);
                            updateProgressByPercentage(tarjeta.atencion_id, tarjeta
                                .porcentaje_progreso);
                            setTimeout(function() {
                                $nueva.removeClass('animar-llegada');
                            }, 500);
                            tarjetasAgregadas++;
                        });
                        if (tarjetasAgregadas > 0) { // Solo actualizar contadores si se agregaron tarjetas
                            ordenarColumna(
                                'columna-recibidas'); // Ordenar tablero después de agregar nuevas tarjetas
                            actualizarContadores();
                            inicializarPopovers(); // Inicializar popovers para las nuevas tarjetas
                        }
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status !== 0) { // Solo log si no es error de red
                        console.error('Error cargando nuevas recibidas:', status);
                    }
                }
            });
        }
        //CONTROL PRINCIPAL
        $(document).ready(function() {
            inicializarPopovers();
            const tarjetasIniciales = { // Datos iniciales de las tarjetas
                recibidas: @json($recibidas),
                progreso: @json($progreso),
                resueltas: @json($resueltas)
            };
            cargarTarjetasIniciales(tarjetasIniciales);

            function initializeProgressBars() {
                $('.progress-divider').each(function() {
                    let atencionId = $(this).data('atencion-id');
                    let avance = $(this).data('avance');
                    if (atencionId && avance !== undefined) {
                        updateProgressByPercentage(atencionId, avance);
                    }
                });
            }
            setTimeout(initializeProgressBars, 100); // Inicializar barras de progreso inmediatamente no es timer
            initKanban();
            let isUpdating = false; // Sistema inteligente de polling para evitar saturación
            let updateInterval = 15000;

            function safeUpdate() {
                if (isUpdating) {
                    return;
                }
                isUpdating = true;
                actualizarAvance(); // Actualizar avances primero
                setTimeout(function() { // Luego cargar nuevas recibidas con delay
                    cargarNuevasRecibidas();
                    isUpdating = false;
                }, 5000);
            }
            safeUpdate(); // Ejecutar inmediatamente al cargar
            setInterval(safeUpdate, updateInterval); // Luego ejecutar cada 30 segundos
        });
    </script>
@endsection
