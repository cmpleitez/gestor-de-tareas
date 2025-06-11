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
                            <h4 class="card-title">EQUIPOS DE TRABAJO</h4>
                            <p class="card-text">Grupos de operadores con diferentes roles y de diferentes areas formados
                                para resolver casos prederminados</p>
                        </div>
                        <div class="col-md-1 d-flex justify-content-end" style="padding: 0;">
                            <a href="{!! route('equipo.create') !!}">
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
                                        <th>Equipo</th>
                                        <th class="text-center">Creado</th>
                                        <th class="text-center">Actualizado</th>
                                        @can('autorizar')<th class="text-center">Estado</th>@endcan
                                        <th class="text-center">Tablero de control</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($equipos as $equipo)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $equipo->id }}</td>
                                            <td>{{ $equipo->equipo }}</td>
                                            <td class="text-center">{{ $equipo->created_at->format('d/m/Y') }}</td>
                                            <td class="text-center">{{ $equipo->updated_at->format('d/m/Y') }}</td>
                                            @can('autorizar')
                                                <td class="text-center">
                                                    <form action="{{ route('equipo.activate', $equipo->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <div class="custom-control custom-switch"
                                                            data-toggle="tooltip" data-placement="top" data-animation="false"
                                                            data-trigger="hover" data-html="true"
                                                            data-title="<i class='bx bxs-error-circle'></i> {{ $equipo->activo ? 'Desactivar' : 'Activar' }} {{ $equipo->equipo }}">
                                                            <input id="activate_{{ $equipo->id }}" type="checkbox" class="custom-control-input" 
                                                                @if($equipo->activo) checked @endif
                                                                onchange="this.form.submit();"
                                                            >
                                                            <label class="custom-control-label" for="activate_{{ $equipo->id }}"></label>
                                                        </div>
                                                    </form>
                                                </td>
                                            @endcan
                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="label">
                                                    {{-- EDITAR --}}
                                                    @can('editar')
                                                        <a href="{{ route('equipo.edit', $equipo->id) }}" role="button"
                                                            data-toggle="tooltip" data-placement="top" data-animation="false"
                                                            data-trigger="hover" data-html="true"
                                                            data-title="<i class='bx bxs-error-circle'></i> Editar datos de {{ $equipo->equipo }}"
                                                            class="button_edit align-center">
                                                            <i class="bx bxs-edit-alt"></i>
                                                        </a>
                                                    @endcan
                                                    {{-- ELIMINAR --}}
                                                    @can('eliminar')
                                                        <a href="{{ route('equipo.destroy', $equipo->id) }}" role="button"
                                                            data-toggle="tooltip" data-placement="top" data-animation="false"
                                                            data-trigger="hover" data-html="true"
                                                            data-title="<i class='bx bxs-eraser'></i> Eliminar {{ $equipo->equipo }}"
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
                                        <th>Equipo</th>
                                        <th class="text-center">Creado</th>
                                        <th class="text-center">Actualizado</th>
                                        @can('autorizar')<th class="text-center">Estado</th>@endcan
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
