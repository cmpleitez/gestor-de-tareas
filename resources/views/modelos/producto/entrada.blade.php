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
                                                        <a href="#" role="button" data-toggle="modal"
                                                            data-target="#modalEntrada" data-producto-id="{{ $producto->id }}"
                                                            data-producto-nombre="{{ $producto->producto }}"
                                                            data-popup="tooltip-custom" data-html="true" data-placement="bottom"
                                                            title="<i class='bx bx-log-in-circle'></i> Agregar entrada a {{ $producto->producto }}"
                                                            class="button_edit align-center border border-secondary text-secondary-dark bg-secondary-light">
                                                            <i class="bx bx-log-in-circle"></i>
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

    {{-- MODAL DE ENTRADA --}}
    <div class="modal fade" id="modalEntrada" tabindex="-1" role="dialog" aria-labelledby="modalEntradaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEntradaLabel">
                        Agregar Entrada
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p id="modalProductoNombre" style="font-size: 1.2rem; font-weight: 500;"></p>
                    
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="unidades">Unidades</label>
                                <input type="number" class="form-control" name="unidades" id="unidades" min="1" step="1" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="ageSelect">Origen</label>
                                <select class="form-control" name="age_select" id="ageSelect">
                                    <option selected disabled>Select your age</option>
                                    <option>12-18</option>
                                    <option>18-22</option>
                                    <option>22-30</option>
                                    <option>30-60</option>
                                    <option>Above 60</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="ageSelect">Destino</label>
                                <select class="form-control" name="age_select" id="ageSelect">
                                    <option selected disabled>Select your age</option>
                                    <option>12-18</option>
                                    <option>18-22</option>
                                    <option>22-30</option>
                                    <option>30-60</option>
                                    <option>Above 60</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
                    "pageLength": 10
                });
            }

            // Inicializar tooltips de Bootstrap 4 con HTML habilitado
            $('[data-toggle="tooltip"]').tooltip({
                html: true,
                placement: 'bottom'
            });

            // Actualizar modal con datos del producto cuando se abre
            $('#modalEntrada').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Botón que activó el modal
                var productoNombre = button.data('producto-nombre'); // Obtener nombre del producto
                var productoId = button.data('producto-id'); // Obtener ID del producto

                // Actualizar el contenido del modal con el nombre del producto
                var modal = $(this);
                modal.find('#modalProductoNombre').text(productoNombre);
            });
        });
    </script>

@stop
