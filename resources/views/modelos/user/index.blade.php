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
                    <h4 class="card-title">USUARIOS DEL SISTEMA</h4>
                    <p class="card-text">Las personas autorizadas para operar el sistema desempeñando roles específicos</p>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive mt-1">
                            <table id="datatable" class="table zero-configuration table-hover">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Rol</th>
                                        <th>Usuario</th>
                                        <th>correo</th>
                                        <th>Creado</th>
                                        <th>Actualizado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td>{{ $user->area->area }}</td>
                                            <td>{{ $user->roles->first()->name }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $user->updated_at->format('d/m/Y') }}</td>
                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-end">
                                                <div class="btn-group" role="group" aria-label="label">
                                                    {{-- MOSTRAR AGENDA --}}
                                                    @can('autorizar')
                                                    <a href="{{ route('user.roles-edit', $user->id) }}" role="button"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-animation="false" data-trigger="hover" data-html="true"
                                                        data-title="<i class='bx bxs-error-circle'></i> Permisos de {{ $user->name }}"
                                                        class="button_keys">
                                                        <i class="bx bxs-key"></i>
                                                    </a>
                                                    @endcan            
                                                    {{-- MOSTRAR PRECIOS --}}
{{--                                                     @can('ver')
                                                        <a href="{{ route('precio', ['area' => $area->id]) }}" role="button"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-animation="false" data-bs-trigger="hover" data-bs-html="true"
                                                            data-bs-title="<i class='fa-beat-fade fa-lg'></i> Mostrar precios del area {{ $area->area }}"
                                                            class="button_show">
                                                            <i class="fas fa-tag"></i>
                                                        </a>
                                                    @endcan --}}
                                                    {{-- CONTROL EDITAR --}}
{{--                                                     @can('editar')
                                                        <a href="{{ route('area.edit', ['area' => $area, 'espacio' => $espacio]) }}"
                                                            role="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-animation="false" data-bs-trigger="hover" data-bs-html="true"
                                                            data-bs-title="<i class='fa-solid fa-triangle-exclamation fa-beat-fade fa-lg'></i> Corregir {{ $area->area }}"
                                                            class="button_edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan --}}
                                                    {{-- CONTROL ELIMINAR --}}
{{--                                                     @can('eliminar')
                                                        <a href="#" role="button"
                                                            onclick="event.preventDefault(); f_confirmar_eliminacion('{{ route('area.destroy', $area->id) }}');"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-animation="false" data-bs-trigger="hover" data-bs-html="true"
                                                            data-bs-title="<i class='fa-solid fa-bolt fa-beat-fade fa-lg'></i> Eliminar {{ $area->area }}"
                                                            class="button_delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    @endcan --}}
                                                </div>
                                            </td>                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Area</th>
                                        <th>Rol</th>
                                        <th>Usuario</th>
                                        <th>correo</th>
                                        <th>Creado</th>
                                        <th>Actualizado</th>
                                        <th>Acciones</th>
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
