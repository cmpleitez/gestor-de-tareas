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
    </style>
@endsection

@section('contenedor')
{{-- ITEMS DESTINATARIOS PARA CADA ROL --}}
<div class="accordion collapse-icon accordion-icon-rotate" id="accordionWrapa2">
    <div class="card collapse-header">
        <div id="heading5" class="card-header" data-toggle="collapse" data-target="#accordion5" aria-expanded="false"
            aria-controls="accordion5" role="tablist">
            <span class="collapse-title">
                <i class="bx bx-cloud align-middle"></i>
                <span class="align-middle text-uppercase">
                    @if(auth()->user()->hasRole('Recepcionista'))
                        AREA: {{ auth()->user()->area->area }}
                    @elseif(auth()->user()->hasRole('Supervisor'))
                        EQUIPO DE TRABAJO: {{ auth()->user()->equipos()->first()->equipo }}
                    @elseif(auth()->user()->hasRole('Gestor'))
                        OPERADOR: {{ auth()->user()->name }}
                    @endif
                </span>
            </span>
        </div>
        <div id="accordion5" role="tabpanel" data-parent="#accordionWrapa2" aria-labelledby="heading5" class="collapse">
            <div class="card-content">
                <div class="card-body">
                    @if (auth()->user()->hasRole('Recepcionista') && isset($areas))
                        <div class="row" style="display: flex; align-items: stretch;">
                            @foreach ($areas as $area)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header text-white">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="area_{{ $area->id }}"
                                                    name="area_destino" class="custom-control-input"
                                                    value="{{ $area->id }}"
                                                    {{ $area->id == auth()->user()->area_id ? 'checked' : '' }}>
                                                <label class="custom-control-label h5 mb-0"
                                                    for="area_{{ $area->id }}"
                                                    style="padding: 10px; cursor: pointer;">
                                                    {{ $area->area }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif (auth()->user()->hasRole('Supervisor') && isset($equipos))
                        <div class="row" style="display: flex; align-items: stretch;">
                            @foreach ($equipos as $equipo)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <div class="custom-control custom-radio">
                                                    <input type="radio" id="equipo_{{ $equipo->id }}"
                                                        name="equipo_destino" class="custom-control-input"
                                                        value="{{ $equipo->id }}"
                                                        {{ $equipo->id == auth()->user()->equipos->first()->id ? 'checked' : '' }}>
                                                <label class="custom-control-label h5 mb-0"
                                                    for="equipo_{{ $equipo->id }}"
                                                    style="padding: 10px; cursor: pointer;">
                                                    {{ $equipo->equipo }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif (auth()->user()->hasRole('Gestor') && isset($operadores))
                        <div class="row" style="display: flex; align-items: stretch;">
                            @foreach ($operadores as $operador)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="operador_{{ $operador->id }}"
                                                    name="operador_destino" class="custom-control-input"
                                                    value="{{ $operador->id }}"
                                                    {{ $operador->id == auth()->user()->id ? 'checked' : '' }}>
                                                <label class="custom-control-label h5 mb-0"
                                                    for="operador_{{ $operador->id }}"
                                                    style="padding: 10px; cursor: pointer;">
                                                    {{ $operador->name }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>No hay elementos disponibles para tu rol.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABLEROS KANBAN --}}
<div class="row" style="display: flex; align-items: stretch;">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">📨 Recibidas</h5>
            </div>
            <div class="card-body kanban-columna">
                <div id="columna-recibidas" class="sortable-column">
                    <div class="text-center text-muted">
                        <i class="bx bx-loader-alt bx-spin"></i> Cargando...
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📦 En Progreso</h5>
            </div>
            <div class="card-body kanban-columna">
                <div id="columna-progreso" class="sortable-column">
                    <div class="text-center text-muted">Vacío por ahora</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">✔️ Resueltas</h5>
            </div>
            <div class="card-body kanban-columna">
                <div id="columna-resueltas" class="sortable-column">
                    <div class="text-center text-muted">Vacío por ahora</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Overlay y Sidebar Kanban --}}
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
        $(document).ready(function() {
            
            // LECTURA DE DATOS
            function cargarTarjetas() {
                $('#columna-recibidas').html(
                    '<div class="text-center text-muted"><i class="bx bx-loader-alt bx-spin"></i> Cargando...</div>'
                );
                $.ajax({
                    url: '{{ route('recepcion.solicitudes') }}',
                    type: 'GET',
                    timeout: 3000,
                    success: function(response) {
                        const recepciones = response.recepciones || [];
                        ubicarTarjetas(recepciones);
                    },
                    error: function(xhr, status, error) {
                        $('#columna-recibidas').html(
                            '<div class="text-center text-danger">Error al cargar solicitudes</div>'
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
                if (contadores.recibidas === 0) { // Mostrar mensajes si alguna columna quedó vacía
                    $('#columna-recibidas').html(
                        '<div class="text-center text-muted">No hay solicitudes recibidas</div>');
                }
                if (contadores.progreso === 0) {
                    $('#columna-progreso').html(
                        '<div class="text-center text-muted">No hay solicitudes en progreso</div>');
                }
                if (contadores.resueltas === 0) {
                    $('#columna-resueltas').html(
                        '<div class="text-center text-muted">No hay solicitudes resueltas</div>');
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
                if (userRole === 'Recepcionista') { 
                    selectedValue = $('input[name="area_destino"]:checked').val();
                    if (!selectedValue) {
                        Swal.fire({
                            position: 'top-end',
                            type: 'warning',
                            title: 'Selecciona un área destino primero',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        // Revertir la tarjeta a su posición original
                        $(evt.from).append(evt.item);
                        return;
                    }
                    url = '{{ route("recepcion.derivar", ["recepcion_id" => ":id", "area_id" => ":area"]) }}'
                        .replace(':id', solicitudId)
                        .replace(':area', selectedValue);
                } else if (userRole === 'Supervisor') { 
                    selectedValue = $('input[name="equipo_destino"]:checked').val();
                    if (!selectedValue) {
                        Swal.fire({
                            position: 'top-end',
                            type: 'warning',
                            title: 'Selecciona un equipo destino primero',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        // Revertir la tarjeta a su posición original
                        $(evt.from).append(evt.item);
                        return;
                    }
                    url = '{{ route("recepcion.asignar", ["recepcion_id" => ":id", "equipo_id" => ":equipo"]) }}'
                        .replace(':id', solicitudId)
                        .replace(':equipo', selectedValue);
                } else if (userRole === 'Gestor') { 
                    selectedValue = $('input[name="operador_destino"]:checked').val();
                    if (!selectedValue) {
                        Swal.fire({
                            position: 'top-end',
                            type: 'warning',
                            title: 'Selecciona un operador destino primero',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        // Revertir la tarjeta a su posición original
                        $(evt.from).append(evt.item);
                        return;
                    }
                    url = '{{ route("recepcion.delegar", ["recepcion_id" => ":id", "user_id" => ":user"]) }}'
                        .replace(':id', solicitudId)
                        .replace(':user', selectedValue);
                } else if (userRole === 'Operador') { 
                    url = '{{ route("recepcion.iniciar-tareas", ["recepcion_id" => ":id"]) }}'
                        .replace(':id', solicitudId);
                }
                
                //ACTUALIZAR ESTADO EN EL BACKEND
                $.ajax({
                    url: url,
                    method: 'POST',
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

                // Rellenar la información en el sidebar
                $('#sidebar-card-title').text(titulo);
                $('#sidebar-card-body').html('<p>' + atencion + '</p>');

                // Mostrar overlay y sidebar
                $('.kanban-overlay').addClass('show');
                $('.kanban-sidebar').addClass('show');
            });

            //Cerrar sidebar al hacer clic en overlay o en el icono de cierre
            $(document).on('click', '.kanban-overlay, .kanban-sidebar .close-icon', function() {
                $('.kanban-overlay').removeClass('show');
                $('.kanban-sidebar').removeClass('show');
            });
            
            //ACTUALIZACIÓN DINÁMICA DEL TÍTULO DEL ACORDEÓN
            $(document).on('change', 'input[name="area_destino"]', function() {
                const areaId = $(this).val();
                const areaNombre = $(this).closest('.card').find('label').text().trim();
                $('.collapse-title span.align-middle').text(`AREA: ${areaNombre}`);
                $('#accordion5').collapse('hide');
            });
            $(document).on('change', 'input[name="equipo_destino"]', function() {
                const equipoId = $(this).val();
                const equipoNombre = $(this).closest('.card').find('label').text().trim();
                $('.collapse-title span.align-middle').text(`EQUIPO DE TRABAJO: ${equipoNombre}`);
                $('#accordion5').collapse('hide');
            });
            $(document).on('change', 'input[name="operador_destino"]', function() {
                const operadorId = $(this).val();
                const operadorNombre = $(this).closest('.card').find('label').text().trim();
                $('.collapse-title span.align-middle').text(`OPERADOR: ${operadorNombre}`);
                $('#accordion5').collapse('hide');
            });
        });
    </script>
@endsection
