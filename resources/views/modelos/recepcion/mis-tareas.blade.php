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

        <!-- Sidebar para ver las tareas de cada solicitud -->
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }

        .kanban-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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
        $(document).ready(function () {

            /* ======================================================
               1)  FUNCIONES DE APOYO
            ====================================================== */

            // Construir la estructura que jKanban necesita a partir del JSON
            function buildKanbanData(recepciones) {
                // Tableros base ─­­ajusta colores/títulos a tu gusto
                const boards = {
                    recibidas:  { id: 'kanban-board-1', title: 'Recibidas',  class: 'bg-light-warning',  item: [] },
                    en_progreso:{ id: 'kanban-board-2', title: 'En Progreso', class: 'bg-light-warning',  item: [] },
                    resueltas:  { id: 'kanban-board-3', title: 'Resueltas',  class: 'bg-light-success',  item: [] },
                };

                recepciones.forEach(r => {
                    // Ajusta los nombres de campos según lo que devuelva tu API
                    const estado = (r.estado_slug || 'recibidas').toLowerCase();
                    if (!boards[estado]) return;                // si llega un estado que no existe, lo ignoro

                    boards[estado].item.push({
                        id      : r.id,                          // id único (string o número)
                        title   : r.titulo || r.detalles || '-', // texto de la tarjeta
                        border  : r.color || 'primary',          // opcional – define color del borde
                        dueDate : r.fecha_limite,                // opcional – fecha de venc.
                        comment : r.total_comentarios,           // opcional – n.º de comentarios
                        users   : r.avatars                      // opcional – array de URLs de avatares
                    });
                });

                // jKanban espera un ARRAY, no un objeto
                return Object.values(boards);
            }

            // Crear el tablero y añadir avatares/fecha/comentarios al pie de cada tarjeta
            function initKanban(boardData) {

                const kanban = new jKanban({
                    element             : '#kanban-app',
                    gutter              : '15px',
                    responsivePercentage: true,
                    addItemButton       : false,
                    dragItems           : true,
                    dragBoards          : false,

                    click: function (el) {                      // mostrar la sidebar de detalles
                        $('.kanban-overlay, .kanban-sidebar').addClass('show');
                        $('.edit-kanban-item-title').val($(el).contents()[0].data);
                    },

                    boards: boardData
                });

                /* ---------- adornar cada tarjeta ---------- */
                boardData.forEach(board => {
                    board.item.forEach(it => {
                        const $el = $(kanban.findElement(it.id));
                        if (!$el.length) return;

                        /* avatares */
                        let htmlUsers = '';
                        (it.users || []).forEach(u => {
                            htmlUsers += `
                                <li class="avatar pull-up my-0">
                                    <img class="media-object rounded-circle" src="${u}" height="24" width="24">
                                </li>`;
                        });

                        /* fecha límite y nº comentarios */
                        const htmlDue = it.dueDate
                            ? `<div class="kanban-due-date d-flex align-items-center mr-50">
                                   <i class="bx bx-time-five font-size-small mr-25"></i>
                                   <span class="font-size-small">${it.dueDate}</span>
                               </div>` : '';

                        const htmlCom = it.comment
                            ? `<div class="kanban-comment d-flex align-items-center mr-50">
                                   <i class="bx bx-message font-size-small mr-25"></i>
                                   <span class="font-size-small">${it.comment}</span>
                               </div>` : '';

                        if (htmlUsers || htmlDue || htmlCom) {
                            $el.append(`
                                <div class="kanban-footer d-flex justify-content-between mt-1">
                                    <div class="kanban-footer-left d-flex">
                                        ${htmlDue}${htmlCom}
                                    </div>
                                    <div class="kanban-footer-right">
                                        <div class="kanban-users">
                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                ${htmlUsers}
                                            </ul>
                                        </div>
                                    </div>
                                </div>`);
                        }
                    });
                });
            }

            /* ======================================================
               2)  PETICIÓN AJAX Y ARRANQUE DEL TABLERO
            ====================================================== */
            $.ajax({
                url     : '{{ route('recepcion.recibidas') }}',
                type    : 'GET',
                dataType: 'json',
                success : res => initKanban( buildKanbanData(res.recepciones || []) ),
                error   : ()  => initKanban( buildKanbanData([]) )      // tablero vacío si falla la petición
            });

            /* ======================================================
               3)  Cerrar la sidebar de detalles
            ====================================================== */
            $('.kanban-sidebar .close-icon, .kanban-overlay').on('click', () => {
                $('.kanban-overlay, .kanban-sidebar').removeClass('show');
            });
        });
    </script>
@endsection
@endsection
