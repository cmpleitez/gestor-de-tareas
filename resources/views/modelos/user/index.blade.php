@extends('dashboard')
@section('css')
@stop

@section('contenedor')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                    <div class="col-11 p-1">
                        <h4 class="card-title">USUARIOS DEL SISTEMA</h4>
                        <p class="card-text">Las personas autorizadas para operar el sistema desempeñando roles
                            específicos</p>
                    </div>
                    @if (auth()->user()->mainRole->name == 'admin')
                    <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                        <a href="{!! route('register') !!}">
                            <div class="badge-circle badge-circle-md bg-primary">
                                <i class="bx bx-plus-medical font-small-3"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive mt-1">
                        <table id="datatable" class="table zero-configuration table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
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
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td>{{ $user->oficina->oficina }}</td>
                                    <td>{{ $user->equipos->pluck('equipo')->first() }}</td>
                                    <td>{{ $user->dui }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill bg-warning">
                                            {{ $user->main_role ?? $user->roles->pluck('name')->first() }}
                                        </span>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $user->updated_at->format('d/m/Y') }}</td>
                                    {{-- ACTIVAR --}}
                                    @can('autorizar')
                                    <td class="text-center">
                                        <form action="{{ route('user.activate', $user->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <div class="custom-control custom-switch" style="transform: scale(0.6); margin: 0;" data-toggle="tooltip" data-html="true" data-placement="bottom" title="<i class='bx bxs-error-circle'></i> {{ $user->activo ? 'Desactivar' : 'Activar' }} {{ $user->name }}">
                                                <input id="activate_{{ $user->id }}" type="checkbox" class="custom-control-input" @if ($user->activo) checked @endif
                                                onchange="this.form.submit();">
                                                <label class="custom-control-label" for="activate_{{ $user->id }}"></label>
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
                                            <a href="{{ route('user.tareas-edit', $user->id) }}" role="button" data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="<i class='bx bx-slider-alt'></i> Actualizar habilidades de {{ $user->name }}" class="button_edit border border-secondary-dark text-secondary-dark bg-secondary-light">
                                                <i class="bx bx-slider-alt"></i>
                                            </a>
                                            {{-- Asignar a equipos --}}
                                            <a href="{{ route('user.equipos-edit', $user->id) }}" role="button" data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="<i class='bx bxs-group'></i> Equipos de {{ $user->name }}" class="button_show border border-secondary-dark text-secondary-dark bg-secondary-light">
                                                <i class="bx bxs-group"></i>
                                            </a>
                                            {{-- Asignar roles --}}
                                            <a href="{{ route('user.roles-edit', $user->id) }}" role="button" data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="<i class='bx bxs-key'></i> Roles de {{ $user->name }}" class="button_keys border border-secondary-dark text-secondary-dark bg-secondary-light">
                                                <i class="bx bxs-key"></i>
                                            </a>
                                            @endcan
                                            {{-- Editar --}}
                                            @can('editar')
                                            <a href="{{ route('user.edit', $user->id) }}" role="button" data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $user->name }}" class="button_edit align-center border border-warning-dark text-warning-dark bg-warning-light">
                                                <i class="bx bxs-edit-alt"></i>
                                            </a>
                                            @endcan
                                            {{-- Eliminar --}}
                                            @can('eliminar')
                                            @if ($user->id !== auth()->id())
                                            <a href="{{ route('user.destroy', $user->id) }}" role="button" data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="<i class='bx bxs-eraser'></i> Eliminar {{ $user->name }}" class="button_delete align-center border border-danger-dark text-danger-dark bg-danger-light">
                                                <i class="bx bxs-eraser"></i>
                                            </a>
                                            @else
                                            <span class="button_delete align-center border border-danger-dark text-danger-dark bg-danger-light" style="opacity: 0.5; cursor: not-allowed;" data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="<i class='bx bxs-lock'></i> No puedes eliminarte a ti mismo">
                                                <i class="bx bxs-trash"></i>
                                            </span>
                                            @endif
                                            @endcan
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Oficina</th>
                                    <th>Equipo</th>
                                    <th>DUI</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Correo</th>
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
@include('components.orientation-manager') {{-- Componente de orientación para tablas --}}
<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('.zero-configuration').DataTable({
                "language": {
                    "url": "/app-assets/Spanish.json"
                }
                , "pageLength": 50
            });
        }
        $('[data-toggle="tooltip"]').tooltip({
            html: true
            , placement: 'bottom'
        });
    });

</script>
@stop
