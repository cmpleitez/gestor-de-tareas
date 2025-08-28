@extends('dashboard')

@section('css')
    <!-- BEGIN: Vendor CSS-->
    <link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
    <!-- END: Vendor CSS-->
@stop

@section('contenedor')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                        <div class="col-11 p-1">
                            <h4 class="card-title">TAREAS</h4>
                            <p class="card-text">Una o más tareas integran una solicitud de servicio, las tareas son las
                                unidades más pequeñas del flujo de trabajo</p>
                        </div>
                        <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                            <a href="{!! route('tarea.create') !!}">
                                <div class="badge-circle badge-circle-md badge-circle-primary">
                                    <i class="bx bx-plus-medical font-small-3"></i>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive mt-1">
                            <table id="datatable" class="table zero-configuration table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Tarea</th>
                                        <th class="text-center">Creado</th>
                                        <th class="text-center">Actualizado</th>
                                        @can('autorizar')
                                            <th class="text-center">Estado</th>
                                        @endcan
                                        <th class="text-center">Tablero de control</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tareas as $tarea)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $tarea->id }}</td>
                                            <td>{{ $tarea->tarea }}</td>
                                            <td class="text-center">{{ $tarea->created_at->format('d/m/Y') }}</td>
                                            <td class="text-center">{{ $tarea->updated_at->format('d/m/Y') }}</td>
                                            {{-- ACTIVAR --}}
                                            @can('autorizar')
                                                <td class="text-center">
                                                    <form action="{{ route('tarea.activate', $tarea->id) }}" method="POST"
                                                        style="display: inline;">
                                                        @csrf
                                                        <div class="custom-control custom-switch"
                                                            style="transform: scale(0.6); margin: 0;" data-toggle="popover"
                                                            data-placement="top" data-html="true"
                                                            data-content="<i class='bx bxs-error-circle'></i> {{ $tarea->activo ? 'Desactivar' : 'Activar' }} {{ $tarea->tarea }}">
                                                            <input id="activate_{{ $tarea->id }}" type="checkbox"
                                                                class="custom-control-input"
                                                                @if ($tarea->activo) checked @endif
                                                                onchange="this.form.submit();">
                                                            <label class="custom-control-label"
                                                                for="activate_{{ $tarea->id }}"></label>
                                                        </div>
                                                    </form>
                                                </td>
                                            @endcan
                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="label">
                                                    {{-- EDITAR --}}
                                                    @can('editar')
                                                        <a href="{{ route('tarea.edit', $tarea->id) }}" role="button"
                                                            data-toggle="popover" data-placement="top" data-html="true"
                                                            data-content="<i class='bx bxs-error-circle'></i> Editar datos de {{ $tarea->tarea }}"
                                                            class="button_edit align-center">
                                                            <i class="bx bxs-edit-alt"></i>
                                                        </a>
                                                    @endcan
                                                    {{-- ELIMINAR --}}
                                                    @can('eliminar')
                                                        <a href="{{ route('tarea.destroy', $tarea->id) }}" role="button"
                                                            data-toggle="popover" data-placement="top" data-html="true"
                                                            data-content="<i class='bx bxs-eraser'></i> Eliminar {{ $tarea->tarea }}"
                                                            class="button_delete align-center">
                                                            <i class="bx bxs-eraser"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Tarea</th>
                                        <th class="text-center">Creado</th>
                                        <th class="text-center">Actualizado</th>
                                        @can('autorizar')
                                            <th class="text-center">Estado</th>
                                        @endcan
                                        <th class="text-center">Tablero de control</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <!-- END: Page Vendor JS-->

    {{-- Componente de orientación para tablas --}}
    @include('components.orientation-manager')

    <script>
        // ===== INICIALIZACIÓN ESPECÍFICA DE POPOVERS PARA TAREAS =====
        $(document).ready(function() {
            // Función para inicializar popovers con clases contextuales
            function initializeTareaPopovers() {
                $('[data-toggle="popover"]').each(function() {
                    var $this = $(this);

                    // Evitar inicializar popovers ya inicializados
                    if ($this.data('bs.popover')) {
                        return;
                    }

                    var content = $this.attr('data-content') || $this.attr('data-title');

                    // Determinar el tipo de popover basado en el contenido
                    var popoverClass = '';
                    if (content && (content.includes('Eliminar') || content.includes('Desactivar'))) {
                        popoverClass = 'popover-danger';
                    } else if (content && (content.includes('Editar'))) {
                        popoverClass = 'popover-warning';
                    } else if (content && (content.includes('Activar'))) {
                        popoverClass = 'popover-success';
                    }

                    try {
                        $this.popover({
                            html: true,
                            container: 'body',
                            trigger: 'hover',
                            template: '<div class="popover ' + popoverClass +
                                '" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>'
                        });

                        console.log('Popover inicializado:', content);
                    } catch (error) {
                        console.error('Error al inicializar popover:', error);
                    }
                });
            }

            // Inicializar DataTable con callback inteligente
            if ($.fn.DataTable) {
                var table = $('.zero-configuration').DataTable({
                    "language": {
                        "url": "/app-assets/Spanish.json"
                    },
                    "responsive": true,
                    "autoWidth": false,
                    "order": [
                        [0, 'asc']
                    ],
                    "pageLength": 50,
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "Todos"]
                    ],
                    "initComplete": function() {
                        // DataTables terminó completamente - reinicializar popovers
                        console.log('DataTables completado - reinicializando popovers');
                        reInitializePopovers();
                    }
                });
            } else {
                console.error('DataTables no está disponible');
            }
        });
    </script>

@stop
