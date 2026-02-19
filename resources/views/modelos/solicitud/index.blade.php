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
                            <h4 class="card-title">SOLICITUDES</h4>
                            <p class="card-text">Son las unidades de servicio brindadas a los clientes del sistema, una
                                solicitud puede tener una o más tareas</p>
                        </div>
                        <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                            <a href="{!! route('solicitud.create') !!}">
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
                                        <th>Solicitud</th>
                                        <th class="text-center">Creado</th>
                                        <th class="text-center">Actualizado</th>
                                        @can('autorizar')
                                            <th class="text-center">Estado</th>
                                        @endcan
                                        <th class="text-center">Tablero de control</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solicitudes as $solicitud)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $solicitud->id }}</td>
                                            <td>{{ $solicitud->solicitud }}</td>
                                            <td class="text-center">{{ $solicitud->created_at->format('d/m/Y h:i a') }}</td>
                                            <td class="text-center">{{ $solicitud->updated_at->format('d/m/Y h:i a') }}</td>
                                            {{-- ACTIVAR --}}
                                            @can('autorizar')
                                                <td class="text-center">
                                                    <form action="{{ route('solicitud.activate', $solicitud->id) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        <div class="custom-control custom-switch"
                                                            style="transform: scale(0.6); margin: 0;" data-toggle="tooltip"
                                                            data-html="true" data-placement="bottom"
                                                            title="<i class='bx bxs-error-circle'></i> {{ $solicitud->activo ? 'Desactivar' : 'Activar' }} {{ $solicitud->solicitud }}">
                                                            <input id="activate_{{ $solicitud->id }}" type="checkbox"
                                                                class="custom-control-input"
                                                                @if ($solicitud->activo) checked @endif
                                                                onchange="this.form.submit();">
                                                            <label class="custom-control-label"
                                                                for="activate_{{ $solicitud->id }}"></label>
                                                        </div>
                                                    </form>
                                                </td>
                                            @endcan
                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="label">
                                                    {{-- EDITAR --}}
                                                    @can('editar')
                                                        <a href="{{ route('solicitud.asignar-tareas', $solicitud->id) }}"
                                                            role="button" data-toggle="tooltip" data-popup="tooltip-custom"
                                                            data-html="true" data-placement="bottom"
                                                            title="<i class='bx bxs-cog'></i> Asignar tareas a {{ $solicitud->solicitud }}"
                                                            class="button_edit align-center border border-secondary-dark text-secondary-dark bg-secondary-light">
                                                            <i class="bx bxs-cog"></i>
                                                        </a>
                                                        <a href="{{ route('solicitud.edit', $solicitud->id) }}" role="button"
                                                            data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                            data-placement="bottom"
                                                            title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $solicitud->solicitud }}"
                                                            class="button_edit align-center border border-warning-dark text-warning-dark bg-warning-light">
                                                            <i class="bx bxs-edit-alt"></i>
                                                        </a>
                                                    @endcan
                                                    {{-- ELIMINAR --}}
                                                    @can('eliminar')
                                                        <a href="{{ route('solicitud.destroy', $solicitud->id) }}"
                                                            role="button" data-toggle="tooltip" data-popup="tooltip-custom"
                                                            data-html="true" data-placement="bottom"
                                                            title="<i class='bx bxs-eraser'></i> Eliminar {{ $solicitud->solicitud }}"
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
                                        <th>Solicitud</th>
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
                    },
                    "pageLength": 50
                });
            }
            $('[data-toggle="tooltip"]').tooltip({
                html: true,
                placement: 'bottom'
            });
        });
    </script>
@stop
