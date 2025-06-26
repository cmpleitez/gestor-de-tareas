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
        <div class="col-xl-2 col-sm-6 col-12">
            <div class="card text-center bg-primary bg-lighten-3">
                <div class="card-content text-white">
                    <div class="card-body">
                        <h4 class="card-title white">Storage Device</h4>
                        <p class="card-text">945 items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                        <div class="col-md-11" style="padding: 0;">
                            <h4 class="card-title">RECEPCIONES</h4>
                            <p class="card-text">Aquí podrás ver las solicitudes que has recibido en tu oficina y las que han sido derivadas a ti</p>
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
                                        <th class="text-center">ID</th>
                                        <th>solicitud</th>
                                        <th>detalles</th>
                                        <th class="text-center">Creado</th>
                                        <th class="text-center">tablero de control</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recepciones as $recepcion)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $recepcion->atencion_id }}</td>
                                            <td>{{ $recepcion->solicitud->solicitud }}</td>
                                            <td>{{ $recepcion->detalles }}</td>
                                            <td class="text-center">{{ $recepcion->created_at->format('d/m/Y') }}</td>
                                            <td class="text-center">

                                                <div class="btn-group" role="group" aria-label="label">
                                                    @can('derivar')
                                                        <button type="button" 
                                                            class="btn btn-link button_show"
                                                            data-toggle="modal" 
                                                            data-target="#derivar"
                                                            data-placement="top" 
                                                            data-animation="false"
                                                            data-trigger="hover" 
                                                            data-html="true"
                                                            data-title="<i class='bx bxs-share-alt'></i> Compartir solicitud con el area respectiva"
                                                            data-solicitud-id="{{ $recepcion->solicitud_id }}"
                                                            data-recepcion-id="{{ $recepcion->id }}">
                                                            <i class="bx bxs-share-alt"></i>
                                                        </button>
                                                    @endcan
                                                </div>


                                                <div class="btn-group" role="group" aria-label="label">
                                                    @can('asignar')
                                                        <button type="button" 
                                                            class="btn btn-link button_show"
                                                            data-toggle="modal" 
                                                            data-target="#derivar"
                                                            data-placement="top" 
                                                            data-animation="false"
                                                            data-trigger="hover" 
                                                            data-html="true"
                                                            data-title="<i class='bx bxs-share-alt'></i> Compartir solicitud con el area respectiva"
                                                            data-solicitud-id="{{ $recepcion->solicitud_id }}"
                                                            data-recepcion-id="{{ $recepcion->id }}">
                                                            <i class="bx bxs-share-alt"></i>
                                                        </button>
                                                    @endcan
                                                </div>



                                                <div class="btn-group" role="group" aria-label="label">
                                                    @can('delegar')
                                                        <button type="button" 
                                                            class="btn btn-link button_show"
                                                            data-toggle="modal" 
                                                            data-target="#derivar"
                                                            data-placement="top" 
                                                            data-animation="false"
                                                            data-trigger="hover" 
                                                            data-html="true"
                                                            data-title="<i class='bx bxs-share-alt'></i> Compartir solicitud con el area respectiva"
                                                            data-solicitud-id="{{ $recepcion->solicitud_id }}"
                                                            data-recepcion-id="{{ $recepcion->id }}">
                                                            <i class="bx bxs-share-alt"></i>
                                                        </button>
                                                    @endcan
                                                </div>


                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>solicitud</th>
                                        <th>detalles</th>
                                        <th class="text-center">Creado</th>
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
    @include('modelos.recepcion.derivar')
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
            $('[data-toggle="tooltip"]').tooltip();  // Inicializa tooltips
            
            // Manejar correctamente la accesibilidad del modal
            $('#derivar').on('show.bs.modal', function() {
                $(this).removeAttr('inert');
                $('body').addClass('modal-open');
            });
            $('#derivar').on('hide.bs.modal', function() {
                $(this).attr('inert', true);
                $('body').removeClass('modal-open');
            });
            $('#derivar').on('hidden.bs.modal', function() {
                $(this).find('.modal-body').empty();
            });
            
            var isLoading = false;
            $(document).on('click', '[data-toggle="modal"][data-target="#derivar"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isLoading) {
                    return; // Evitar múltiples llamadas mientras se carga
                }
                var solicitudId = $(this).data('solicitud-id');
                var recepcionId = $(this).data('recepcion-id');
                mostrarCargando(); // Mostrar indicador de carga
                isLoading = true;
                $.ajax({
                    url: '{{ route('recepcion.area', ['solicitud' => ':solicitudId']) }}'.replace(':solicitudId', solicitudId),
                    method: 'GET',
                    timeout: 10000, // 10 segundos de timeout
                    success: function(data) {
                        console.log('Datos recibidos:', data);
                        actualizarModal(data, recepcionId);
                        abrirModal();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        
                        var errorMsg = 'Error al cargar las áreas';
                        if (status === 'timeout') {
                            errorMsg = 'La solicitud tardó demasiado tiempo. Intente nuevamente.';
                        } else if (xhr.status === 404) {
                            errorMsg = 'No se encontró la información solicitada.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Error interno del servidor.';
                        }
                        
                        actualizarModal([]);
                        $('#derivar .modal-body').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                        abrirModal();
                    },
                    complete: function() {
                        isLoading = false;
                    }
                });
            });
            
            function mostrarCargando() {
                $('#derivar .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
            }
            
            function abrirModal() {
                $('#derivar').modal({
                    backdrop: 'static',
                    keyboard: true,
                    focus: true
                });
            }
            
            function actualizarModal(data, recepcionId) {
                var modalBody = $('#derivar .modal-body');
                modalBody.empty();
                
                var areas = data.areas;
                var cantidad_operadores = data.cantidad_operadores;
                
                if (areas.length > 0) {
                    var html = '';
                    areas.forEach(function(area) {
                        var url = '{{ route('recepcion.derivar', ['recepcion' => ':recepcionId', 'area' => ':areaId']) }}'
                            .replace(':recepcionId', recepcionId)
                            .replace(':areaId', area.id);
                        
                        html += '<div class="row">'+
                        '<div class="col-xl-2 col-sm-6 col-12">'+
                            '<a href="' + url + '">'+
                                '<div class="card text-center bg-primary bg-lighten-2" style="margin-bottom: 0.2rem;">'+
                                    '<div class="card-content text-white">'+
                                        '<div class="card-body">'+
                                            '<h4 class="card-title white">'+area.area+'</h4>'+
                                            '<p class="card-text">'+cantidad_operadores+' Operador(es) activo(s)</p>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</a>'+
                        '</div>';
                    });
                    modalBody.html(html);
                } else {
                    modalBody.html('<div>No se encontraron áreas para esta solicitud.</div>');
                }
            }
        });
    </script>
    <!-- END: Page Vendor JS-->
@stop
