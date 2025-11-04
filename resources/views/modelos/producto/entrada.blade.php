@extends('dashboard')

@section('css')
    <link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
@stop

@section('contenedor')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                        <div class="col-11 p-1">
                            <h4 class="card-title">ENTRADAS</h4>
                            <p class="card-text">Registro de entradas de productos a los diferentes stocks</p>
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
                                        <th>Producto</th>
                                        <th class="text-center">Modelo</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Producto/Accesorio</th>
                                        <th class="text-center">Tablero de control</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productos as $producto)
                                        <tr>
                                            {{-- CAMPOS --}}
                                            <td class="text-center">{{ $producto->id }}</td>
                                            <td>{{ $producto->producto }}</td>
                                            <td class="text-center">{{ $producto->modelo->modelo }}</td>
                                            <td class="text-center">{{ $producto->tipo->tipo }}</td>
                                            <td class="text-center">{{ $producto->accesorio ? 'Accesorio' : 'Producto' }}
                                            </td>

                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="label">
                                                    {{-- AGREGAR ENTRADA --}}
                                                    @can('editar')
                                                        <a href="#" role="button"
                                                            data-toggle="tooltip" data-popup="tooltip-custom" data-html="true" data-placement="bottom"
                                                            title="<i class='bx bx-truck'></i> Agregar entrada a {{ $producto->producto }}"
                                                            class="button_edit align-center border border-warning-dark text-warning-dark bg-warning-light">
                                                            <i class="bx bxs-truck"></i>
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
                                        <th>Producto</th>
                                        <th class="text-center">Modelo</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Producto/Accesorio</th>
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
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

    {{-- Componente de orientación para tablas --}}
    @include('components.orientation-manager')

    <script>
        // ===== CONFIGURACIÓN MÍNIMA Y FUNCIONAL =====
        $(document).ready(function() {

            // Inicializar DataTable de forma básica
            if ($.fn.DataTable) {
                $('.zero-configuration').DataTable({
                    "language": {
                        "url": "/app-assets/Spanish.json"
                    },
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
