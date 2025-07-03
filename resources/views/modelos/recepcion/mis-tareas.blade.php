@extends('dashboard')

@section('contenedor')

<!-- Kanban Overlay -->
<div class="kanban-overlay"></div>
<!-- Kanban Section -->
<section id="kanban-wrapper">
    <div class="row">
        <div class="col-12">
            <div id="kanban-app"></div>
        </div>
    </div>

                <!-- Sidebar para ver detalles de tareas -->
            <div class="kanban-sidebar">
                <div class="card shadow-none">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom px-2 py-1">
                        <h3 class="card-title">Detalles de la Tarea</h3>
                        <button type="button" class="close close-icon">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <!-- Información de la tarea -->
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-group">
                                <label><strong>Título:</strong></label>
                                <p class="edit-kanban-item-title">-</p>
                            </div>
                            <div class="form-group">
                                <label><strong>Estado:</strong></label>
                                <p class="task-status">-</p>
                            </div>
                            <div class="form-group">
                                <label><strong>Fecha de Vencimiento:</strong></label>
                                <p class="task-due-date">-</p>
                            </div>
                            <div class="form-group">
                                <label><strong>Comentarios:</strong></label>
                                <p class="task-comments">-</p>
                            </div>
                            <div class="form-group">
                                <label><strong>Usuarios Asignados:</strong></label>
                                <div class="task-users">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</section>

@section('js')
<!-- Kanban CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/jkanban/jkanban.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-kanban.css') }}">

<!-- CSS Responsivo personalizado -->
<style>
    /* Estilos responsivos para el Kanban */
    .kanban-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }
    
    .kanban-board {
        flex: 1;
        min-width: 280px;
        max-width: 350px;
        margin: 0 !important;
    }
    
    /* Tablet */
    @media (max-width: 768px) {
        .kanban-board {
            min-width: 250px;
            max-width: 100%;
        }
        
        .kanban-container {
            gap: 10px;
        }
        
        #kanban-wrapper {
            padding: 0 10px;
        }
    }
    
    /* Móvil */
    @media (max-width: 576px) {
        .kanban-board {
            min-width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }
        
        .kanban-container {
            flex-direction: column;
            gap: 0;
        }
        
        #kanban-wrapper {
            padding: 0 5px;
        }
        
        .kanban-sidebar {
            width: 100% !important;
            right: 0 !important;
        }
    }
    
    /* Mejorar el aspecto visual */
    .kanban-board-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px 8px 0 0;
        padding: 15px;
        font-weight: 600;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
    }
    
    .kanban-item {
        margin-bottom: 10px;
        padding: 12px;
        background: white;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }
    
    .kanban-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .kanban-item[data-border="info"] {
        border-left-color: #17a2b8;
    }
    
    .kanban-item[data-border="warning"] {
        border-left-color: #ffc107;
    }
    
    .kanban-item[data-border="success"] {
        border-left-color: #28a745;
    }
    
    .kanban-item[data-border="danger"] {
        border-left-color: #dc3545;
    }
    
    .kanban-item[data-border="primary"] {
        border-left-color: #007bff;
    }
    
    .kanban-drag {
        min-height: 200px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }
    
    /* Estilos para el footer de las tareas */
    .kanban-footer {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e9ecef;
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .kanban-due-date,
    .kanban-comment {
        display: flex;
        align-items: center;
        margin-right: 10px;
    }
    
    .kanban-users .avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-left: -8px;
        border: 2px solid white;
    }
    
    .kanban-users .avatar:first-child {
        margin-left: 0;
    }
</style>

<!-- Kanban JS -->
<script src="{{ asset('app-assets/vendors/js/jkanban/jkanban.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Variables globales para el kanban
    var kanban_curr_el, kanban_curr_item_id, kanban_item_title;

    // Datos de ejemplo para las tareas - Aquí puedes cargar datos desde tu base de datos
    var kanban_board_data = [
        { //PENDIENTES
            id: "kanban-board-1",
            title: "Pendientes",
            class: "bg-light-info",
            item: [
                {
                    id: "1",
                    title: "Revisar documentos del cliente",
                    border: "info",
                    dueDate: "{{ date('M d') }}",
                    comment: 2,
                    users: ["{{ asset('app-assets/images/portrait/small/avatar-s-11.jpg') }}"]
                },
                {
                    id: "2",
                    title: "Preparar informe mensual",
                    border: "warning",
                    dueDate: "{{ date('M d', strtotime('+3 days')) }}",
                    comment: 1
                }
            ]
        },
        { //EN PROCESO
            id: "kanban-board-2",
            title: "En Progreso",
            class: "bg-light-warning",
            item: [
                {
                    id: "3",
                    title: "Desarrollar nueva funcionalidad",
                    border: "primary",
                    dueDate: "{{ date('M d', strtotime('+5 days')) }}",
                    comment: 5,
                    users: [
                        "{{ asset('app-assets/images/portrait/small/avatar-s-12.jpg') }}",
                        "{{ asset('app-assets/images/portrait/small/avatar-s-13.jpg') }}"
                    ]
                }
            ]
        },
        { //RESUELTAS
            id: "kanban-board-3",
            title: "Resueltas",
            class: "bg-light-success",
            item: [
                {
                    id: "4",
                    title: "Reunión con el equipo",
                    border: "success",
                    dueDate: "{{ date('M d', strtotime('-1 day')) }}",
                    comment: 3,
                    users: ["{{ asset('app-assets/images/portrait/small/avatar-s-14.jpg') }}"]
                }
            ]
        }
    ];

    // Inicializar el Kanban (solo visualización)
    var KanbanTareas = new jKanban({
        element: "#kanban-app", // Contenedor del kanban
        gutter: "15px", // Espaciado entre columnas
        responsivePercentage: true, // Hacer columnas responsivas
        addItemButton: false, // Desactivar botón de agregar tareas
        
        // Configuración de arrastrar y soltar
        dragItems: true, // Permitir arrastrar tareas entre columnas
        dragBoards: false, // No permitir mover columnas
        
        // Evento al hacer clic en una tarea
        click: function(el) {
            // Mostrar overlay y sidebar para ver detalles
            $(".kanban-overlay").addClass("show");
            $(".kanban-sidebar").addClass("show");
            
            // Guardar elemento actual
            kanban_curr_el = el;
            kanban_item_title = $(el).contents()[0].data;
            kanban_curr_item_id = $(el).attr("data-eid");
            
            // Llenar formulario con datos actuales
            $(".edit-kanban-item .edit-kanban-item-title").val(kanban_item_title);
        },
        
        // Evento cuando se mueve una tarea (para actualizar estado)
        dragendItem: function(el) {
            var taskId = $(el).attr("data-eid");
            var newStatus = $(el).closest(".kanban-board").attr("data-id");
            console.log("Tarea " + taskId + " movida a " + newStatus);
            
            // Aquí puedes agregar código AJAX para actualizar el estado en la base de datos
            // $.post('/api/tareas/actualizar-estado', {
            //     task_id: taskId,
            //     new_status: newStatus
            // });
        },
        
        // Datos de los tableros
        boards: kanban_board_data
    });

    // Agregar elementos visuales personalizados a las tareas
    var board_item_id, board_item_el;
    for (var kanban_data in kanban_board_data) {
        for (var kanban_item in kanban_board_data[kanban_data].item) {
            var board_item_details = kanban_board_data[kanban_data].item[kanban_item];
            board_item_id = board_item_details.id;
            board_item_el = KanbanTareas.findElement(board_item_id);
            
            var board_item_users = "";
            var board_item_dueDate = "";
            var board_item_comment = "";
            
            // Agregar usuarios
            if (board_item_details.users) {
                for (var user_idx in board_item_details.users) {
                    board_item_users += 
                        '<li class="avatar pull-up my-0">' +
                        '<img class="media-object rounded-circle" src="' + board_item_details.users[user_idx] + '" alt="Avatar" height="24" width="24">' +
                        '</li>';
                }
            }
            
            // Agregar fecha de vencimiento
            if (board_item_details.dueDate) {
                board_item_dueDate = 
                    '<div class="kanban-due-date d-flex align-items-center mr-50">' +
                    '<i class="bx bx-time-five font-size-small mr-25"></i>' +
                    '<span class="font-size-small">' + board_item_details.dueDate + '</span>' +
                    '</div>';
            }
            
            // Agregar comentarios
            if (board_item_details.comment) {
                board_item_comment = 
                    '<div class="kanban-comment d-flex align-items-center mr-50">' +
                    '<i class="bx bx-message font-size-small mr-25"></i>' +
                    '<span class="font-size-small">' + board_item_details.comment + '</span>' +
                    '</div>';
            }
            
            // Agregar footer con información adicional
            if (board_item_details.dueDate || board_item_details.comment || board_item_details.users) {
                $(board_item_el).append(
                    '<div class="kanban-footer d-flex justify-content-between mt-1">' +
                    '<div class="kanban-footer-left d-flex">' +
                    board_item_dueDate + board_item_comment +
                    '</div>' +
                    '<div class="kanban-footer-right">' +
                    '<div class="kanban-users">' +
                    '<ul class="list-unstyled users-list m-0 d-flex align-items-center">' +
                    board_item_users +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            }
        }
    }

    // No se necesita código para agregar columnas

    // Cerrar sidebar
    $(".kanban-sidebar .close-icon, .kanban-overlay").on("click", function() {
        $(".kanban-overlay").removeClass("show");
        $(".kanban-sidebar").removeClass("show");
    });
});
</script>
@endsection
@endsection
