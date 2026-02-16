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
                        <h4 class="card-title">PRODUCTOS</h4>
                        <p class="card-text">Los productos son los artículos que forman parte de los kits compatibles para marcas específicas</p>
                    </div>
                    <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                        <a href="{!! route('producto.create') !!}">
                            <div class="badge-circle badge-circle-md btn-warning">
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
                                    <th>Codigo</th>
                                    <th>Producto</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Precio</th>
                                    <th class="text-center">Creado</th>
                                    <th class="text-center">Actualizado</th>
                                    @can('autorizar')
                                    <th class="text-center">Estado</th>
                                    @endcan
                                    <th class="text-center">Tablero de control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                <tr>
                                    {{-- CAMPOS --}}
                                    <td class="text-center">{{ $producto->id }}</td>
                                    <td>{{ $producto->codigo }}</td>
                                    <td>{{ $producto->producto }}</td>
                                    <td>{{ $producto->modelo->modelo }}</td>
                                    <td>{{ $producto->tipo->tipo }}</td>
                                    <td class="td-currency">{{ $producto->precio }}</td>
                                    <td class="text-center">{{ $producto->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $producto->updated_at->format('d/m/Y') }}</td>
                                    {{-- ACTIVAR --}}
                                    @can('autorizar')
                                    <td class="text-center">
                                        <form action="{{ route('producto.activate', $producto->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <div class="custom-control custom-switch"
                                                style="transform: scale(0.6); margin: 0;" data-toggle="tooltip"
                                                data-html="true" data-placement="bottom"
                                                title="<i class='bx bxs-error-circle'></i> {{ $producto->activo ? 'Desactivar' : 'Activar' }} {{ $producto->producto }}">
                                                <input id="activate_{{ $producto->id }}" type="checkbox"
                                                    class="custom-control-input" @if ($producto->activo) checked @endif
                                                onchange="this.form.submit();">
                                                <label class="custom-control-label"
                                                    for="activate_{{ $producto->id }}"></label>
                                            </div>
                                        </form>
                                    </td>
                                    @endcan
                                    {{-- TABLERO DE CONTROL --}}
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="label">
                                            {{-- EDITAR --}}
                                            @can('editar')
                                            <a href="{{ route('producto.edit', $producto->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $producto->producto }}"
                                                class="button_edit align-center border border-warning-dark text-warning-dark bg-warning-light">
                                                <i class="bx bxs-edit-alt"></i>
                                            </a>
                                            @endcan
                                            {{-- ELIMINAR --}}
                                            @can('eliminar')
                                            <a href="{{ route('producto.destroy', $producto->id) }}" role="button"
                                                data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                data-placement="bottom"
                                                title="<i class='bx bxs-eraser'></i> Eliminar {{ $producto->producto }}"
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
                                    <th>Codigo</th>
                                    <th>Producto</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Precio</th>
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
                //INICIALIZACION DE DATATABLES
                if ($.fn.DataTable) {
                    $('.zero-configuration').DataTable({
                        "language": { "url": "/app-assets/Spanish.json" },
                        "pageLength": 10,
                        "columnDefs": [
                            {
                                "targets": 0,
                                "type": "num"
                            },
                        ],
                        "drawCallback": function(settings) {
                            // Formatear celdas con clase .td-currency después de que DataTables dibuje la tabla
                            if (typeof formatCurrencyCells !== 'undefined') {
                                formatCurrencyCells(true); // true = forzar reformateo
                            }
                        }
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