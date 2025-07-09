@extends('dashboard')
@section('contenedor')
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
@endsection

@section('js')
    <!-- Incluir SortableJS para drag & drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

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

    <script>
        $(document).ready(function() {
            // LECTURA DE DATOS
            const CACHE_KEY = 'kanban_solicituds_recibidas';
            const CACHE_TTL = 60000; // 1 minuto en ms
            function guardarEnCache(datos) {
                const payload = {
                    data: datos,
                    timestamp: Date.now()
                };
                localStorage.setItem(CACHE_KEY, JSON.stringify(payload));
            }
            function leerDeCache() {
                const raw = localStorage.getItem(CACHE_KEY);
                if (!raw) return null;
                try {
                    const payload = JSON.parse(raw);
                    if (Date.now() - payload.timestamp < CACHE_TTL) {
                        return payload.data;
                    } else {
                        localStorage.removeItem(CACHE_KEY);
                        return null;
                    }
                } catch {
                    localStorage.removeItem(CACHE_KEY);
                    return null;
                }
            }
            function cargarSolicitudes() { // Actualizar en segundo plano
                const cache = leerDeCache();
                if (cache) {
                    mostrarTarjetas(cache);
                    cargarDesdeServidor(true); 
                } else {
                    cargarDesdeServidor(false);
                }
            }
            function cargarDesdeServidor(silencioso) {
                if (!silencioso) {
                    $('#columna-recibidas').html('<div class="text-center text-muted"><i class="bx bx-loader-alt bx-spin"></i> Cargando...</div>');
                }
                $.ajax({
                    url: '{{ route('recepcion.recibidas') }}',
                    type: 'GET',
                    timeout: 10000,
                    success: function(response) {
                        const recepciones = response.recepciones || [];
                        guardarEnCache(recepciones);
                        mostrarTarjetas(recepciones);
                    },
                    error: function(xhr, status, error) {
                        if (!silencioso) {
                            $('#columna-recibidas').html('<div class="text-center text-danger">Error al cargar solicituds</div>');
                        }
                    }
                });
            }
            function mostrarTarjetas(recepciones) {
                if (recepciones.length === 0) {
                    $('#columna-recibidas').html('<div class="text-center text-muted">No hay solicituds recibidas</div>');
                    return;
                }
                $('#columna-recibidas, #columna-progreso, #columna-resueltas').empty();
                recepciones.forEach(function(recepcion, index) {
                    const tarjetaHtml = `
                        <div class="solicitud-card" data-id="${recepcion.id}">
                            <div class="solicitud-titulo">${recepcion.titulo || recepcion.detalles || 'Sin título'}</div>
                            <div class="solicitud-id">ID: ${recepcion.id}</div>
                            <div class="solicitud-estado" style="font-size: 11px; color: #28a745; margin-top: 5px;">
                                Estado: ${recepcion.estado || 'Recibida'}
                            </div>
                        </div>`;
                    $('#columna-recibidas').append(tarjetaHtml);
                });
                initKanban();
            }

            // INICIALIZACIÓN
            cargarSolicitudes();

            // INICIALIZACIÓN DEL DRAG & DROP
            function initKanban() {
                const columnas = ['columna-recibidas', 'columna-progreso', 'columna-resueltas'];
                columnas.forEach(function(columnaId) {
                    const elemento = document.getElementById(columnaId);
                    if (!elemento) return;
                    new Sortable(elemento, {
                        group: 'kanban', // Permite mover entre columnas
                        animation: 150, // Velocidad de la animación
                        ghostClass: 'sortable-ghost', // Elemento en posición original
                        chosenClass: 'sortable-chosen', // Elemento seleccionado  
                        dragClass: 'sortable-drag', // Elemento siendo arrastrado
                        onMove: function(evt) { //columna de destino con colores más tenues
                            document.querySelectorAll('.sortable-column').forEach(col => {
                                col.style.borderColor = 'transparent';
                            });
                            evt.to.style.borderColor = '#d1ecf1'; // Azul muy tenue
                            evt.to.style.backgroundColor = '#f8fdff'; // Fondo casi imperceptible
                        },
                        onEnd: function(evt) { //Quitar resaltado de todas las columnas
                            document.querySelectorAll('.sortable-column').forEach(col => {
                                col.style.borderColor = col.children.length === 0 ? '#e9ecef' : 'transparent';
                                col.style.backgroundColor = col.children.length === 0 ? '#f8f9fa' : 'transparent';
                            });
                            const solicitudId = evt.item.dataset.id;
                            const columnaOrigen = evt.from.id;
                            const columnaDestino = evt.to.id;
                            if (columnaOrigen !== columnaDestino) {
                                showMoveAlert(solicitudId, columnaDestino);
                            }
                        }
                    });
                });
            }

            //MOSTRAR EL CAMBIO DE ESTADO
            function showMoveAlert(solicitudId, nuevaColumna) {
                let nuevoEstado = '';
                switch (nuevaColumna) {
                    case 'columna-recibidas':
                        nuevoEstado = 'Recibida';
                        break;
                    case 'columna-progreso':
                        nuevoEstado = 'En Progreso';
                        break;
                    case 'columna-resueltas':
                        nuevoEstado = 'Resuelta';
                        break;
                }
                
                console.log('🔄 Actualizando estado:', {
                    solicitudId: solicitudId,
                    nuevoEstado: nuevoEstado,
                    columna: nuevaColumna
                });
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    url: '{{ route('recepcion.update', ['recepcion' => ':id', 'estado' => ':estado']) }}'
                        .replace(':id', solicitudId)
                        .replace(':estado', encodeURIComponent(nuevoEstado)),
                    method: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('✅ Respuesta exitosa:', response);
                        if (response.success) {
                            alert('✅ Estado actualizado a: ' + nuevoEstado);
                        } else {
                            alert('❌ Error al actualizar el estado: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('🚨 Error AJAX completo:', {
                            xhr: xhr,
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            responseJSON: xhr.responseJSON
                        });
                        
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
                        
                        alert(mensaje);
                    }
                });
            }

            //MOSTRAR ACTIVIDADES EN LA PERSIANA: Click para mostrar las actividades en la persiana que se abre a la derecha de la vista 
            $(document).on('click', '.solicitud-card', function() {
                const id = $(this).data('id');
                alert('Tarea clickeada: ' + id);
            });
        });
    </script>
@endsection
