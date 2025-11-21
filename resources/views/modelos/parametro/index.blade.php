@extends('dashboard')
@section('css')
@stop

@section('contenedor')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                    <div class="col-12 p-1">
                        <h4 class="card-title">PARAMETROS</h4>
                        <p class="card-text">Parámetros de configuración del sistema</p>
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
                                    <th>Parámetro</th>
                                    <th>Valor</th>
                                    <th>Unidad de medida</th>
                                    <th class="text-center">Creado</th>
                                    <th class="text-center">Actualizado</th>
                                    @can('autorizar')
                                        <th class="text-center">Estado</th>
                                    @endcan
                                    <th class="text-center">Tablero de control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parametros as $parametro)
                                <tr>
                                    {{-- CAMPOS --}}
                                    <td class="text-center">{{ $parametro->id }}</td>
                                    <td>{{ $parametro->parametro }}</td>
                                    <td>{{ $parametro->valor }}</td>
                                    <td>{{ $parametro->unidad_medida }}</td>
                                    <td class="text-center">{{ $parametro->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $parametro->updated_at->format('d/m/Y') }}</td>
                                    {{-- ACTIVAR --}}
                                    @can('autorizar')
                                    <td class="text-center">
                                        <form action="{{ route('security.parametros-activate', $parametro->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <div class="custom-control custom-switch"
                                                style="transform: scale(0.6); margin: 0;" data-toggle="tooltip"
                                                data-html="true" data-placement="bottom"
                                                title="<i class='bx bxs-error-circle'></i> {{ $parametro->activo ? 'Desactivar' : 'Activar' }} {{ $parametro->parametro }}">
                                                <input id="activate_{{ $parametro->id }}" type="checkbox"
                                                    class="custom-control-input" @if ($parametro->activo) checked @endif
                                                onchange="this.form.submit();">
                                                <label class="custom-control-label"
                                                    for="activate_{{ $parametro->id }}"></label>
                                            </div>
                                        </form>
                                    </td>
                                    @endcan
                                    {{-- TABLERO DE CONTROL --}}
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="label">
                                            {{-- EDITAR --}}
                                            @can('editar')
                                            <a href="{{ route('security.parametros-edit', $parametro->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $parametro->parametro }}"
                                                class="button_edit align-center">
                                                <i class="bx bxs-edit-alt"></i>
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
                                    <th>Parámetro</th>
                                    <th>Valor</th>
                                    <th>Unidad de medida</th>
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