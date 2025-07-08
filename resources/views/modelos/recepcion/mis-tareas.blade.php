@extends('dashboard')

@section('contenedor')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Mis Tareas - Versión Simple</h4>
            </div>
            <div class="card-body">
                <!-- Debug: Mostrar datos crudos -->
                <div id="debug-info" style="background: #f8f9fa; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                    <strong>Datos del servidor:</strong>
                    <pre id="datos-raw"></pre>
                </div>

                <!-- Tablero Kanban básico -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">📥 Recibidas</h5>
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
                                <h5 class="mb-0">⚡ En Progreso</h5>
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
                                <h5 class="mb-0">✅ Completadas</h5>
                            </div>
                            <div class="card-body kanban-columna">
                                <div id="columna-completadas" class="sortable-column">
                                    <div class="text-center text-muted">Vacío por ahora</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
.tarea-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    cursor: move;
    transition: all 0.2s;
    border-left: 4px solid #007bff;
}
.tarea-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}
.tarea-titulo {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 14px;
}
.tarea-id {
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
.sortable-column {
    min-height: 380px; /* Área de drop fija y grande */
    border: 2px dashed transparent;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.sortable-column:empty {
    border-color: #e9ecef; /* Borde visible cuando está vacía */
    background: #f8f9fa;
}
.sortable-ghost {
    opacity: 0.4;
    background: #f0f0f0;
}
.sortable-drag {
    opacity: 1;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    transform: rotate(5deg);
}
</style>

<script>
$(document).ready(function() {
    console.log('🚀 Iniciando carga de tareas...');
    
    // Hacer petición AJAX súper simple
    $.ajax({
        url: '{{ route('recepcion.recibidas') }}',
        type: 'GET',
        timeout: 10000,
        success: function(response) {
            console.log('✅ Respuesta del servidor:', response);
            
            // Mostrar datos crudos para debug
            $('#datos-raw').text(JSON.stringify(response, null, 2));
            
            // Procesar recepciones
            const recepciones = response.recepciones || [];
            console.log('📋 Total recepciones:', recepciones.length);
            
            if (recepciones.length === 0) {
                $('#columna-recibidas').html('<div class="text-center text-muted">No hay tareas recibidas</div>');
                return;
            }
            
            // Limpiar todas las columnas
            $('#columna-recibidas, #columna-progreso, #columna-completadas').empty();
            
            recepciones.forEach(function(recepcion, index) {
                console.log(`📄 Procesando tarea ${index + 1}:`, recepcion);
                
                const tarjetaHtml = `
                    <div class="tarea-card" data-id="${recepcion.id}">
                        <div class="tarea-titulo">${recepcion.titulo || recepcion.detalles || 'Sin título'}</div>
                        <div class="tarea-id">ID: ${recepcion.id}</div>
                        <div class="tarea-estado" style="font-size: 11px; color: #28a745; margin-top: 5px;">
                            Estado: ${recepcion.estado_slug || 'recibida'}
                        </div>
                    </div>
                `;
                
                $('#columna-recibidas').append(tarjetaHtml);
            });
            
                         console.log('✅ Tareas cargadas correctamente');
             
             // Inicializar Kanban después de cargar los datos
             initKanban();
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar tareas:', {xhr, status, error});
            $('#columna-recibidas').html(`
                <div class="alert alert-danger">
                    <strong>Error:</strong> No se pudieron cargar las tareas<br>
                    <small>Status: ${status} | Error: ${error}</small>
                </div>
            `);
            $('#datos-raw').text('Error: ' + error);
        }
    });
    
    // Función para inicializar el drag & drop
    function initKanban() {
        console.log('🎯 Inicializando Kanban drag & drop...');
        
        // Configurar cada columna para drag & drop
        const columnas = ['columna-recibidas', 'columna-progreso', 'columna-completadas'];
        
        columnas.forEach(function(columnaId) {
            const elemento = document.getElementById(columnaId);
            if (!elemento) return;
            
            new Sortable(elemento, {
                group: 'kanban', // Permite mover entre columnas
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                
                // Mejorar feedback visual para columnas vacías
                onMove: function(evt) {
                    // Resaltar la columna de destino con colores más tenues
                    document.querySelectorAll('.sortable-column').forEach(col => {
                        col.style.borderColor = 'transparent';
                    });
                    evt.to.style.borderColor = '#d1ecf1'; // Azul muy tenue
                    evt.to.style.backgroundColor = '#f8fdff'; // Fondo casi imperceptible
                },
                
                onEnd: function(evt) {
                    // Quitar resaltado de todas las columnas
                    document.querySelectorAll('.sortable-column').forEach(col => {
                        col.style.borderColor = col.children.length === 0 ? '#e9ecef' : 'transparent';
                        col.style.backgroundColor = col.children.length === 0 ? '#f8f9fa' : 'transparent';
                    });
                    
                    const tareaId = evt.item.dataset.id;
                    const columnaOrigen = evt.from.id;
                    const columnaDestino = evt.to.id;
                    
                    console.log(`📦 Tarea ${tareaId} movida de ${columnaOrigen} a ${columnaDestino}`);
                    
                    if (columnaOrigen !== columnaDestino) {
                        showMoveAlert(tareaId, columnaDestino);
                    }
                }
            });
        });
        
        console.log('✅ Kanban inicializado correctamente');
    }
    
    // Función para mostrar el cambio de estado
    function showMoveAlert(tareaId, nuevaColumna) {
        let nuevoEstado = '';
        switch(nuevaColumna) {
            case 'columna-recibidas': nuevoEstado = 'Recibida'; break;
            case 'columna-progreso': nuevoEstado = 'En Progreso'; break;
            case 'columna-completadas': nuevoEstado = 'Completada'; break;
        }
        // Aquí harías el AJAX para actualizar en el servidor
    }
    
    // Evento click en tareas (súper simple)
    $(document).on('click', '.tarea-card', function() {
        const id = $(this).data('id');
        alert('Tarea clickeada: ' + id);
    });
});
</script>
@endsection
