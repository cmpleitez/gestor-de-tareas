@extends('dashboard')

@section('css')
<!-- BEGIN: Vendor CSS-->
<link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
<!-- END: Vendor CSS-->

<style>
    /* CSS personalizado para tooltips con borde y sombra */
    .tooltip .tooltip-inner {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
        font-weight: 500 !important;
        max-width: 300px !important;
    }

    .tooltip-inner i {
        margin: 0 !important;
        font-size: 14px !important;
        line-height: 1 !important;
        color: #eeff03 !important;
    }
</style>


@stop

@section('contenedor')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">

                <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                    <div class="col-md-11 p-1">
                        <h4 class="card-title">USUARIOS DEL SISTEMA</h4>
                        <p class="card-text">Las personas autorizadas para operar el sistema desempeñando roles
                            específicos</p>
                    </div>
                </div>

            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive mt-1">
                        <table id="datatable" class="table zero-configuration table-hover">
                            <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>Oficina</th>
                                    <th>Equipo</th>
                                    <th>DUI</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>correo</th>
                                    <th class="text-center">Creado</th>
                                    <th class="text-center">Actualizado</th>
                                    @can('autorizar')
                                    <th class="text-center">Estado</th>
                                    @endcan
                                    <th class="text-center">Tablero de control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    {{-- CAMPOS --}}
                                    <td>{{ $user->area->area }}</td>
                                    <td>{{ $user->area->oficina->oficina }}</td>
                                    <td>{{ $user->equipos->pluck('equipo')->first() }}</td>
                                    <td>{{ $user->dui }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <span class="badge badge-pill badge-light-warning"
                                            style="color: rgb(170, 95, 34) !important;">
                                            {{ $user->main_role ?? $user->roles->pluck('name')->first() }}
                                        </span>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $user->updated_at->format('d/m/Y') }}</td>
                                    {{-- ACTIVAR --}}
                                    @can('autorizar')
                                    <td class="text-center">
                                        <form action="{{ route('user.activate', $user->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <div class="custom-control custom-switch"
                                                style="transform: scale(0.6); margin: 0;" data-toggle="tooltip"
                                                data-html="true" data-placement="bottom"
                                                title="<i class='bx bxs-error-circle'></i> {{ $user->activo ? 'Desactivar' : 'Activar' }} {{ $user->name }}">
                                                <input id="activate_{{ $user->id }}" type="checkbox"
                                                    class="custom-control-input" @if ($user->activo) checked @endif
                                                onchange="this.form.submit();">
                                                <label class="custom-control-label"
                                                    for="activate_{{ $user->id }}"></label>
                                            </div>
                                        </form>
                                    </td>
                                    @endcan
                                    {{-- TABLERO DE CONTROL --}}
                                    @if ($user->activo)
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="label">
                                            @can('autorizar')
                                            {{-- Actualizar habilidades --}}
                                            <a href="{{ route('user.solicitudes-edit', $user->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bx-slider-alt'></i> Actualizar habilidades de {{ $user->name }}"
                                                class="button_show">
                                                <i class="bx bx-slider-alt"></i>
                                            </a>
                                            {{-- Asignar a equipos --}}
                                            <a href="{{ route('user.equipos-edit', $user->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-group'></i> Equipos de {{ $user->name }}"
                                                class="button_show">
                                                <i class="bx bxs-group"></i>
                                            </a>
                                            {{-- Asignar roles --}}
                                            <a href="{{ route('user.roles-edit', $user->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-key'></i> Roles de {{ $user->name }}"
                                                class="button_keys">
                                                <i class="bx bxs-key"></i>
                                            </a>
                                            @endcan
                                            {{-- Editar --}}
                                            @can('editar')
                                            <a href="{{ route('user.edit', $user->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $user->name }}"
                                                class="button_edit align-center">
                                                <i class="bx bxs-edit-alt"></i>
                                            </a>
                                            @endcan
                                            {{-- Eliminar --}}
                                            @can('eliminar')
                                            <a href="{{ route('user.destroy', $user->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-eraser'></i> Eliminar {{ $user->name }}"
                                                class="button_delete align-center">
                                                <i class="bx bxs-eraser"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Area</th>
                                    <th>Oficina</th>
                                    <th>Equipo</th>
                                    <th>DUI</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>correo</th>
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

<script>
    // ===== CONFIGURACIÓN MÍNIMA Y FUNCIONAL =====
        $(document).ready(function() {
            
            // Inicializar DataTable de forma básica
            if ($.fn.DataTable) {
                $('.zero-configuration').DataTable({
                    "language": { "url": "/app-assets/Spanish.json" },
                    "pageLength": 50
                });
            }
            
                                                         // Inicializar tooltips de Bootstrap 4 con HTML habilitado
                                $('[data-toggle="tooltip"]').tooltip({
                                    html: true,
                                    placement: 'bottom'
                                });
        });
</script>

@stop