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
