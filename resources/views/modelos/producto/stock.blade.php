@extends('dashboard')

{{-- LIBRERIAS --}}
@section('css')
    <link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
@stop

@section('contenedor')
    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                        <div class="col-11 p-1">
                            <h4 class="card-title">STOCKS</h4>
                            <p class="card-text">Registro de entradas y salidas de productos a los diferentes stocks</p>
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
                                            {{-- TABLERO DE CONTROL --}}
                                            <td class="text-center">
                                                @can('editar')
                                                    {{-- AGREGAR ENTRADA --}}
                                                    <div class="btn-group" role="group" aria-label="label">
                                                        <a href="#" role="button" data-toggle="modal"
                                                            data-target="#modalMovimiento" data-producto-id="{{ $producto->id }}"
                                                            data-producto-nombre="{{ $producto->producto }}"
                                                            data-popup="tooltip-custom" data-html="true" data-placement="bottom"
                                                            title="<i class='bx bx-log-in-circle'></i> Agregar entrada a {!! $producto->producto !!}"
                                                            class="button_edit align-center border border-secondary text-secondary-dark bg-secondary-light">
                                                            <i class="bx bx-log-in-circle"></i>
                                                        </a>
                                                    </div>
                                                @endcan
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

    {{-- MODAL PARA PROCESAR MOVIMIENTOS --}}
    <div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-labelledby="modalMovimientoLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <form id="formMovimiento" action="{{ route('tienda.store-stock') }}" method="POST" novalidate>
                    @csrf
                    <div class="d-flex flex-column h-100">
                        <input type="hidden" name="producto_id" id="producto_id" value="{{ old('producto_id') }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalMovimientoLabel">
                                Stocks del producto
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row p-1">
                                <div class="col-12">
                                    <h5 id="ProductoNombre" class="font-weight-bold mb-2 p-1"></h5>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div id="ProductoStocks" class="row g-2">
                                            {{-- Grid de stocks por producto --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding: 0 2.1rem 0 2.1rem;">
                                <div class="col-12 col-md-2">
                                    <div class="form-group">
                                        <label for="unidades">Unidades</label>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="unidades"
                                                id="unidades" value="{{ old('unidades') }}"
                                                data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 0, 'rightAlign': false"
                                                data-validation-regex-regex="^[1-9]\d*(?:,\d{3})*$"
                                                data-validation-regex-message="Cero no es válido" required
                                                data-validation-required-message="Este campo es obligatorio">
                                            @error('unidades')
                                                <div class="col-sm-12 badge bg-warning text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('unidades') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5">
                                    <div class="form-group">
                                        <label for="origen_stock_id">Desde</label>
                                        <div class="controls">
                                                <select class="form-control" name="origen_stock_id" id="origen_stock_id" required
                                                    form="formMovimiento">
                                                @foreach ($stocks as $stock)
                                                    <option value="{{ $stock->id }}"
                                                        {{ old('origen_stock_id') == $stock->id ? 'selected': '' }}>
                                                        {{ $stock->stock }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('origen_stock_id')
                                                <div class="col-sm-12 badge bg-warning text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('origen_stock_id') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5">
                                    <div class="form-group">
                                        <label for="destino_stock_id">Hacia</label>
                                        <div class="controls">
                                                <select class="form-control" name="destino_stock_id" id="destino_stock_id"
                                                    required form="formMovimiento">
                                                @foreach ($stocks as $stock)
                                                    <option value="{{ $stock->id }}"
                                                        {{ old('destino_stock_id') == $stock->id
                                                            ? 'selected'
                                                            : '' }}>
                                                        {{ $stock->stock }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('destino_stock_id')
                                                <div class="col-sm-12 badge bg-warning text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('destino_stock_id') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="btnRegistrar">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    {{-- LIBRERÍAS --}}
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    {{-- ORIENTACIÓN PARA TABLAS EN MÓVILES --}}
    @include('components.orientation-manager')
    <script>
        $(document).ready(function() {
            //VALIDACION
            $('#destino_stock_id').val('2');
            $('#origen_stock_id').on('change', function() {
                var origenVal = $(this).val();
                var destinoVal = $('#destino_stock_id').val();
                if ((origenVal == '1' || origenVal == '2')) {
                    if (origenVal == '1') {
                        $('#destino_stock_id').val('2');
                        destinoVal = '2';
                    }
                    if (origenVal == '2') {
                        $('#destino_stock_id').val('1');
                        destinoVal = '1';
                    }
                }
            });
            $('#destino_stock_id').on('change', function() {
                var destinoVal = $(this).val();
                var origenVal = $('#origen_stock_id').val();
                if ((destinoVal == '1' || destinoVal == '2')) {
                    if (destinoVal == '1') {
                        $('#origen_stock_id').val('2');
                        origenVal = '2';
                    }
                    if (destinoVal == '2') {
                        $('#origen_stock_id').val('1');
                        origenVal = '1';
                    }
                }
            });
            $('#formMovimiento').on('submit', function(e) { //Stocks origen y destino deden ser diferentes
                var origenVal = $('#origen_stock_id').val(); 
                var destinoVal = $('#destino_stock_id').val();
                if (origenVal && destinoVal && origenVal === destinoVal) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof toastr !== 'undefined') {
                        toastr.error('El stock origen y destino no deben ser iguales', '', {
                            positionClass: 'toast-bottom-right'
                        });
                    }
                    return false;
                }
            });
            @if ($errors->any())
                $('#modalMovimiento').modal('show');
            @endif
            // INICIALIZACION DE DATATABLES
            if ($.fn.DataTable) {
                $('.zero-configuration').DataTable({
                    "language": {
                        "url": "/app-assets/Spanish.json"
                    },
                    "pageLength": 10,
                    "columnDefs": [
                        {
                            "targets": 0,
                            "type": "num"
                        }
                    ],
                });
            }
            // INICIALIZAR TOOLTIPS
            $('[data-toggle="tooltip"], [data-popup="tooltip-custom"]').tooltip({
                html: true,
                placement: 'bottom'
            });
            $(document).on('show.bs.tooltip', '[data-toggle="tooltip"], [data-popup="tooltip-custom"]', function() {
                $('.tooltip').remove();
                $('[data-toggle="tooltip"], [data-popup="tooltip-custom"]').not(this).tooltip('hide');
            });
            //DIBUJANDO DATOS EN EL MODAL
            $('#modalMovimiento').on('show.bs.modal', function(
                event) {
                var button = $(event.relatedTarget);
                var productoNombre = button.data('producto-nombre');
                var productoId = button.data('producto-id');
                var modal = $(this);
                modal.find('#ProductoNombre').text(productoNombre);
                modal.find('#producto_id').val(productoId);
                var stocksWrapper = modal.find('#ProductoStocks');
                stocksWrapper.empty().append(
                    $('<div>', { class: 'col-12 text-center text-muted' }).text('Cargando stocks...')
                );
                $.ajax({
                    url: '{{ route('tienda.get-stocks-producto', ['productoId' => ':productoId']) }}'.replace(':productoId', productoId),
                    type: 'GET',
                    success: function(response) {
                        stocksWrapper.empty();
                        if (response && Array.isArray(response.stocks) && response.stocks.length) {
                            response.stocks.forEach(function(stock) {
                                var nombre = stock.nombre || 'Stock sin nombre';
                                var unidades = typeof stock.unidades !== 'undefined' ? stock.unidades : 0;
                                var col = $('<div>', {
                                    class: (response.stocks.length === 1 ? 'col-12' : 'col-6 col-md-6 ' + (response.stocks.length <= 2 ? 'col-lg-6' : 'col-lg-4')) + ' mb-2 d-flex'
                                });
                                var card = $('<div>', {
                                    class: 'border rounded p-2 w-100'
                                });
                                var headerRow = $('<div>', { class: 'mb-75' }).append(
                                    $('<div>', {
                                        class: 'text-primary mb-0 font-weight-semibold'
                                    }).text(nombre)
                                );
                                var unidadesRow = $('<div>', { class: 'd-flex justify-content-between align-items-center' });
                                unidadesRow.append(
                                    $('<span>', {
                                        class: 'badge badge-pill ' + ((unidades == 0 && stock.id != 1) ? 'badge-warning' : 'badge-primary')
                                    }).append(
                                        $('<span>', { class: 'mb-0' }).text('Stock '),
                                        unidades
                                    )
                                );
                                card.append(headerRow, unidadesRow);
                                col.append(card);
                                stocksWrapper.append(col);
                            });
                        } else {
                            stocksWrapper.append(
                                $('<div>', { class: 'col-12 text-center text-muted' }).text('Sin stocks registrados.')
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Log:: [Usuario: {{ auth()->user()->name }}] No fue posible cargar los stocks:", error);
                        stocksWrapper.empty().append(
                            $('<div>', { class: 'col-12 text-center text-warning' }).text('No fue posible cargar los stocks.')
                        );
                    }
                });
            });
            $('#modalMovimiento').on('hidden.bs.modal', function() {
                $(this).find('#ProductoStocks').empty();
            });
        });
        
    </script>

@stop
