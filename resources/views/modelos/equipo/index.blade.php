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
                        <h4 class="card-title">EQUIPOS DE TRABAJO</h4>
                        <p class="card-text">Grupos de operadores con diferentes roles y de diferentes areas formados
                            para resolver casos prederminados</p>
                    </div>
                    <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                        @can('crear')
                        <a href="{!! route('equipo.create') !!}">
                            <div class="badge-circle badge-circle-md bg-primary">
                                <i class="bx bx-plus-medical font-small-3"></i>
                            </div>
                        </a>
                        @endcan
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
                                    @can('autorizar')
                                    <th class="text-center">Estado</th>
                                    @endcan
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
                                    {{-- ACTIVAR --}}
                                    @can('autorizar')
                                    <td class="text-center">
                                        <form action="{{ route('equipo.activate', $equipo->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <div class="custom-control custom-switch"
                                                style="transform: scale(0.6); margin: 0;" data-toggle="tooltip"
                                                data-html="true" data-placement="bottom"
                                                title="<i class='bx bxs-error-circle'></i> {{ $equipo->activo ? 'Desactivar' : 'Activar' }} {{ $equipo->equipo }}">
                                                <input id="activate_{{ $equipo->id }}" type="checkbox"
                                                    class="custom-control-input" @if ($equipo->activo) checked @endif
                                                onchange="this.form.submit();">
                                                <label class="custom-control-label"
                                                    for="activate_{{ $equipo->id }}"></label>
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
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $equipo->equipo }}"
                                                class="button_edit align-center border border-warning-dark text-warning-dark bg-warning-light">
                                                <i class="bx bxs-edit-alt"></i>
                                            </a>
                                            @endcan
                                            {{-- ELIMINAR --}}
                                            @can('eliminar')
                                            <a href="{{ route('equipo.destroy', $equipo->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-eraser'></i> Eliminar {{ $equipo->equipo }}"
                                                class="button_delete align-center border border-danger-dark text-danger-dark bg-danger-light">
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
                // INICIALIZACION DE DATATABLES
                if ($.fn.DataTable) {
                    $('.zero-configuration').DataTable({
                        "language": { "url": "/app-assets/Spanish.json" },
                        "pageLength": 50,
                        "columnDefs": [
                            {
                                "targets": 0,
                                "type": "num"
                            }
                        ],                    
                    });
                }
                // INICIALIZAR TOOLTIPS
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                    placement: 'bottom'
                });
            });
    </script>
@stop