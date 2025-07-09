@extends('dashboard')

@section('css')
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/datatables.min.css">
    <!-- Font Awesome CSS from CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- END: Vendor CSS-->
@stop

@section('contenedor')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12 d-flex justify-content-between mt-1" style="padding: 0;">
                        <div class="col-md-11" style="padding: 0;">
                            <h4 class="card-title">RECEPCIONES</h4>
                            <p class="card-text">Aquí podrás ver las solicitudes que has recibido en tu oficina y las que
                                han sido derivadas a ti</p>
                        </div>
                        @can('crear')
                            <div class="col-md-1 d-flex justify-content-end" style="padding: 0;">
                                <a href="{{ route('recepcion.create') }}">
                                    <div class="badge-circle badge-circle-md badge-circle-primary">
                                        <i class="bx bx-plus-medical font-small-3"></i>
                                    </div>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive mt-1">
                            <table id="datatable" class="table zero-configuration table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Fecha de recepción</th>
                                        <th class="text-center">N° de atención</th>
                                        <th>Participantes</th>
                                        <th>solicitud</th>
                                        <th>detalle</th>
                                        <th class="text-center">Distribución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recepciones as $recepcion)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $recepcion->created_at->diffForHumans() }}</td>
                                            <td class="text-center">{{ $recepcion->atencion_id }}</td>
                                            <td>
                                                <div class="widget-todo-item-action d-flex align-items-center">
                                                    <div class="avatar">
                                                        @if ($recepcion->usuarioDestino->profile_photo_path && Storage::disk('public')->exists($recepcion->usuarioDestino->profile_photo_path))
                                                            <img src="{{ Storage::url($recepcion->usuarioDestino->profile_photo_path) }}" alt="avatar" style="height: 32px; width: 32px; object-fit: cover;" data-toggle="tooltip" data-placement="top" title="{{ $recepcion->usuarioDestino->name }} -> {{$recepcion->role->name}}">
                                                        @else
                                                            <img src="{{ asset('app-assets/images/pages/operador.png') }}" alt="avatar" style="height: 28px; width: 28px; object-fit: cover;" data-toggle="tooltip" data-placement="top" title="{{ $recepcion->usuarioDestino->name }} -> {{$recepcion->role->name}}">
                                                        @endif
                                                    </div>
                                                    <div class="badge badge-pill badge-light-primary">{{ $recepcion->area->area }}</div>
                                                </div>
                                            </td>
                                            <td>{{ $recepcion->solicitud->solicitud }}</td>
                                            <td>{{ $recepcion->detalle }}</td>
                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="label">
                                                    @can('derivar')
                                                        <button type="button" class="btn btn-link button_show"
                                                            data-toggle="modal" data-target="#distribuir" data-action="areas"
                                                            data-solicitud-id="{{ $recepcion->solicitud_id }}"
                                                            data-recepcion-id="{{ $recepcion->id }}" title="Derivar a las áreas de trabajo">
                                                            <i class="bx bxs-landmark"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                                <div class="btn-group" role="group" aria-label="label">
                                                    @can('asignar')
                                                        <button type="button" class="btn btn-link button_show"
                                                            data-toggle="modal" data-target="#distribuir" data-action="equipos"
                                                            data-solicitud-id="{{ $recepcion->solicitud_id }}"
                                                            data-recepcion-id="{{ $recepcion->id }}" title="Asignar a equipos de trabajo">
                                                            <i class="bx bxs-group"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                                <div class="btn-group" role="group" aria-label="label">
                                                    @can('delegar')
                                                        <button type="button" class="btn btn-link button_show"
                                                            data-toggle="modal" data-target="#distribuir"
                                                            data-action="operadores"
                                                            data-solicitud-id="{{ $recepcion->solicitud_id }}"
                                                            data-recepcion-id="{{ $recepcion->id }}" title="Delegar a operadores">
                                                            <i class="bx bxs-user"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">Fecha de recepción</th>
                                        <th class="text-center">N° de atención</th>
                                        <th>Participantes</th>
                                        <th>solicitud</th>
                                        <th>detalle</th>
                                        <th class="text-center">Distribución</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modelos.recepcion.distribuir')
@stop

@section('js')
    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip(); // Inicializa tooltips
            $('.button_show').tooltip({ container: 'body', placement: 'top' }); // Tooltips para los botones de acción
            $('#distribuir').on('show.bs.modal', function() { // Manejar correctamente la accesibilidad del modal
                $(this).removeAttr('inert');
                $('body').addClass('modal-open');
            });
            $('#distribuir').on('hide.bs.modal', function() {
                $(this).attr('inert', true);
                $('body').removeClass('modal-open');
            });
            $('#distribuir').on('hidden.bs.modal',
                function() { // Limpiar contenido de la modal cuando se cierre completamente
                    $(this).find('.modal-body').empty();
                });
            var isLoading = false;
            $(document).on('click', '[data-toggle="modal"][data-target="#distribuir"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isLoading) {
                    return; // Evitar múltiples llamadas mientras se carga
                }
                var solicitudId = $(this).data('solicitud-id');
                var recepcionId = $(this).data('recepcion-id');
                var action = $(this).data('action'); // Obtener el tipo de acción

                mostrarCargando(); // Mostrar indicador de carga en momento de lentitud de la red
                isLoading = true;


                var ajaxUrl = ''; // Determinar la URL según la acción
                var errorContext = '';
                switch (action) {
                    case 'areas':
                        ajaxUrl = '{{ route('recepcion.areas', ['solicitud' => ':solicitudId']) }}'
                            .replace(':solicitudId', solicitudId);
                        errorContext = 'áreas';
                        break;
                    case 'equipos':
                        ajaxUrl = '{{ route('recepcion.equipos', ['solicitud' => ':solicitudId']) }}'
                            .replace(':solicitudId', solicitudId);
                        errorContext = 'equipos';
                        break;
                    case 'operadores':
                        ajaxUrl = '{{ route('recepcion.operadores', ['solicitud' => ':solicitudId']) }}'
                            .replace(':solicitudId', solicitudId);
                        errorContext = 'operadores';
                        break;
                    default:
                        ajaxUrl = '{{ route('recepcion.areas', ['solicitud' => ':solicitudId']) }}'
                            .replace(':solicitudId', solicitudId);
                        errorContext = 'datos';
                }

                //Dibujando datos del modal de distribución
                $.ajax({
                    url: ajaxUrl,
                    method: 'GET',
                    timeout: 10000,
                    success: function(data) {
                        actualizarModal(data, recepcionId, action);
                        abrirModal();
                    },
                    error: function(xhr, status, error) {
                        var errorMsg = 'Error al cargar los ' + errorContext;
                        if (status === 'timeout') {
                            errorMsg =
                                'La solicitud tardó demasiado tiempo. Intente nuevamente.';
                        } else if (xhr.status === 404) {
                            errorMsg = 'No se encontró la información solicitada.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Error interno del servidor.';
                        }
                        actualizarModal([], recepcionId, action);
                        $('#distribuir .modal-body').html('<div class="alert alert-danger">' +
                            errorMsg + '</div>');
                        abrirModal();
                    },
                    complete: function() {
                        isLoading = false;
                    }
                });
            });

            function mostrarCargando() {
                $('#distribuir .modal-body').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
            }

            function abrirModal() {
                $('#distribuir').modal({
                    backdrop: 'static',
                    keyboard: true,
                    focus: true
                });
            }

            function actualizarModal(data, recepcionId, action) {
                var modalBody = $('#distribuir .modal-body');
                modalBody.empty();

                switch (action) {
                    case 'areas':
                        renderAreas(data, recepcionId, modalBody);
                        break;
                    case 'equipos':
                        renderEquipos(data, recepcionId, modalBody);
                        break;
                    case 'operadores':
                        renderOperadores(data, recepcionId, modalBody);
                        break;
                    default:
                        renderAreas(data, recepcionId, modalBody);
                }
            }

            function renderAreas(data, recepcionId, modalBody) {
                var areas = data.areas;
                var cantidad_operadores = data.cantidad_operadores;

                if (areas.length > 0) {
                    var html = '';
                    areas.forEach(function(area) {
                        var url =
                            '{{ route('recepcion.derivar', ['recepcion' => ':recepcionId', 'area' => ':areaId']) }}'
                            .replace(':recepcionId', recepcionId)
                            .replace(':areaId', area.id);

                        html += '<div class="col-xl-3 col-sm-6 col-12" style="margin-bottom: 0.5rem;">' +
                            '<a href="' + url + '" class="text-decoration-none">' +
                            '<div class="card text-center bg-primary bg-lighten-2 h-100">' +
                            '<div class="card-content text-white">' +
                            '<div class="card-body">' +
                            '<i class="bx bxs-buildings font-large-2 mb-1"></i>' +
                            '<h5 class="card-title white mb-1">' + area.area + '</h5>' +
                            '<p class="card-text small">' + cantidad_operadores +
                            ' Operador(es) activo(s)</p>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</a>' +
                            '</div>';
                    });
                    modalBody.html('<div class="row">' + html + '</div>');
                } else {
                    modalBody.html(
                        '<div class="alert alert-info">No se encontraron áreas disponibles para esta solicitud.</div>'
                    );
                }
            }

            function renderEquipos(data, recepcionId, modalBody) {
                var equipos = data.equipos;
                var unidades = data.unidades;
                if (equipos.length > 0) {
                    var html = '';
                    equipos.forEach(function(equipo) {
                        var url =
                            '{{ route('recepcion.asignar', ['recepcion' => ':recepcionId', 'equipo' => ':equipoId']) }}'
                            .replace(':recepcionId', recepcionId)
                            .replace(':equipoId', equipo.id);

                        html += '<div class="col-xl-3 col-sm-6 col-12" style="margin-bottom: 0.5rem;">' +
                            '<a href="' + url + '" class="text-decoration-none">' +
                            '<div class="card text-center bg-success bg-lighten-2 h-100">' +
                            '<div class="card-content text-white">' +
                            '<div class="card-body">' +
                            '<i class="bx bxs-group font-large-2 mb-1"></i>' +
                            '<h5 class="card-title white mb-1">' + equipo.equipo + '</h5>' +
                            '<p class="card-text small">' + unidades +
                            ' unidades de capacidad técnica</p>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</a>' +
                            '</div>';
                    });
                    modalBody.html('<div class="row">' + html + '</div>');
                } else {
                    modalBody.html(
                        '<div class="alert alert-info">No se encontraron equipos disponibles en tu área.</div>');
                }
            }

            function renderOperadores(data, recepcionId, modalBody) {
                var operadores = data.operadores;
                if (operadores.length > 0) {
                    var html = '';
                    operadores.forEach(function(operador) {
                        var url =
                            '{{ route('recepcion.delegar', ['recepcion' => ':recepcionId', 'user' => ':userId']) }}'
                            .replace(':recepcionId', recepcionId)
                            .replace(':userId', operador.id);

                        html += '<div class="col-xl-3 col-sm-6 col-12" style="margin-bottom: 0.5rem;">' +
                            '<a href="' + url + '" class="text-decoration-none">' +
                            '<div class="card text-center bg-warning bg-lighten-2 h-100">' +
                            '<div class="card-content text-white">' +
                            '<div class="card-body">' +
                            '<i class="bx bxs-user font-large-2 mb-1"></i>' +
                            '<h5 class="card-title white mb-1">' + operador.name + '</h5>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</a>' +
                            '</div>';
                    });
                    modalBody.html('<div class="row">' + html + '</div>');
                } else {
                    modalBody.html(
                        '<div class="alert alert-info">No se encontraron operadores disponibles en tu equipo.</div>'
                    );
                }
            }
        });
    </script>
    <!-- END: Page Vendor JS-->
@stop
