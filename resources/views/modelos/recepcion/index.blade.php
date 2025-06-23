@extends('dashboard')

@section('css')
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/datatables.min.css">
    <!-- END: Vendor CSS-->
@stop

@section('contenedor')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                    <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                        <div class="col-md-11" style="padding: 0;">
                            <h4 class="card-title">SOLICITUDES RECIBIDAS</h4>
                            <p class="card-text">Aquí podrás ver las solicitudes que han sido recibidas</p>
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
                                        @can('activar')<th class="text-center">Validar</th>@endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recepciones as $recepcion)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $recepcion->id }}</td>
                                            <td>{{ $recepcion->solicitud->solicitud }}</td>
                                            <td>{{ $recepcion->detalles }}</td>
                                            <td class="text-center">{{ $recepcion->created_at->format('d/m/Y') }}</td>
                                            {{-- ACTIVAR --}}
                                            @can('activar')
                                                <td class="text-center">
                                                    <form action="{{ route('recepcion.activate', $recepcion->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <div class="custom-control custom-switch" style="transform: scale(0.6); margin: 0;"
                                                            data-toggle="tooltip" data-placement="top" data-animation="false"
                                                            data-trigger="hover" data-html="true"
                                                            data-title="<i class='bx bxs-error-circle'></i> {{ $recepcion->activo ? 'Invalidar' : 'Validar' }} {{ $recepcion->solicitud->solicitud }}">
                                                            <input id="activate_{{ $recepcion->id }}" type="checkbox" class="custom-control-input" 
                                                                @if($recepcion->activo) checked @endif
                                                                onchange="this.form.submit();"
                                                            >
                                                            <label class="custom-control-label" for="activate_{{ $recepcion->id }}"></label>
                                                        </div>
                                                    </form>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>solicitud</th>
                                        <th>detalles</th>
                                        <th class="text-center">Creado</th>
                                        @can('activar')<th class="text-center">Validar</th>@endcan
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
    <script src="/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <!-- END: Page Vendor JS-->
@stop