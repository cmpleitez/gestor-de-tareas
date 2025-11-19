@extends('dashboard')

@section('css')
<link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
<style>
    #datatable_wrapper .row:first-child {
        margin-bottom: 1rem;
    }

    #datatable_wrapper .row:last-child {
        margin-top: 1rem;
    }

    #datatable tbody tr {
        display: flex;
        width: 100%;
    }

    #datatable tbody tr td {
        flex: 1;
        width: 50%;
    }

    #datatable tbody tr td:empty {
        min-height: 1px;
    }

</style>
@stop

@section('contenedor')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                    <div class="col-11 p-1">
                        <h4 class="card-title">ASIGNAR PRODUCTOS AL KIT: {{ $kit->kit }}</h4>
                        <p class="card-text">Selecciona los productos que formarán parte de este kit. Los productos ya asignados aparecen marcados.</p>
                    </div>
                    <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                        <a href="{{ route('kit') }}">
                            <div class="badge-circle badge-circle-md btn-secondary">
                                <i class="bx bx-arrow-back font-small-3"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive mt-1">
                        <table id="datatable" class="table zero-configuration">
                            <thead style="display: none;"></thead>
                            <tbody>
                                @foreach ($productosChunks as $chunk)
                                <tr>
                                    @foreach ($chunk as $producto)
                                    <td class="product-card-container">
                                        <div class="product-card {{ in_array($producto->id, $kitProductosIds) ? 'border-primary-dark text-warning-dark bg-warning-light' : '' }}" data-producto-id="{{ $producto->id }}">
                                            @if(in_array($producto->id, $kitProductosIds))
                                            <span class="product-card-badge">Asignado</span>
                                            @endif
                                            <input type="checkbox" class="card-checkbox product-checkbox" data-producto-id="{{ $producto->id }}" {{ in_array($producto->id, $kitProductosIds) ? 'checked' : '' }}>
                                            <div class="product-card-header">
                                                <h5 class="product-card-title">{{ $producto->producto }}</h5>
                                            </div>
                                            <div class="product-card-body">
                                                <div class="product-card-info">
                                                    <span class="product-card-label">ID</span>
                                                    <span class="product-card-value">{{ $producto->id }}</span>
                                                </div>
                                                <div class="product-card-info">
                                                    <span class="product-card-label">Modelo</span>
                                                    <span class="product-card-value">{{ $producto->modelo->modelo }}</span>
                                                </div>
                                                <div class="product-card-info">
                                                    <span class="product-card-label">Tipo</span>
                                                    <span class="product-card-value">{{ $producto->tipo->tipo }}</span>
                                                </div>
                                                @if($producto->accesorio)
                                                <div class="product-card-info">
                                                    <span class="product-card-label">Accesorio</span>
                                                    <span class="product-card-value">
                                                        <i class="bx bx-check text-success"></i>
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endforeach
                                    @if($chunk->count() == 1)
                                    <td class="product-card-container"></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<button type="button" class="btn btn-success btn-lg save-products-btn" id="saveProductsBtn">
    <i class="bx bx-save"></i> Guardar
</button>

<form id="assignProductsForm" action="{{ route('kit.actualizar-productos', $kit->id) }}" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <div id="selectedProductsContainer"></div>
</form>
@if($errors->any())
toastr.error('{{ $errors->first() }}', 'Error');
@endif
@if(session('success'))
toastr.success('{{ session('success ') }}', 'Éxito');
@endif
@if(session('error'))
toastr.error('{{ session('error ') }}', 'Error');
@endif
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

<script>
    $(document).ready(function() {
        // INICIALIZACION DE DATATABLES
        if ($.fn.DataTable) {
            var table = $('.zero-configuration').DataTable({
                "language": {
                    "url": "/app-assets/Spanish.json"
                }
                , "pageLength": 10
                , "searching": true
                , "paging": true
                , "ordering": false
                , "order": []
                , "info": true
                , "columnDefs": [{
                    "targets": [0, 1]
                    , "orderable": false
                    , "searchable": true
                }]
                , "drawCallback": function(settings) {
                    attachCardListeners(); // Re-aplicar event listeners después de que DataTables redibuje
                    initializeSelectedProducts(); // Actualizar estado de productos asignados después de cada redibujado
                    updateSaveButton();
                }
                , "initComplete": function(settings, json) {
                    initializeSelectedProducts(); // Inicializar estado de productos asignados cuando DataTables termina de cargar
                    attachCardListeners();
                    updateSaveButton();
                }
            });
        }

        // FUNCIONES PARA MANEJAR LA SELECCION DE PRODUCTOS
        function attachCardListeners() { // Aplicar event listeners a las tarjetas
            $('.product-card').off('click').on('click', function(e) {
                if ($(e.target).is('.card-checkbox') || $(e.target).closest('.card-checkbox').length) {
                    return;
                }
                var checkbox = $(this).find('.product-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                toggleCardSelection($(this), checkbox.prop('checked'));
            });
            $('.product-checkbox').off('change').on('change', function() {
                var card = $(this).closest('.product-card');
                toggleCardSelection(card, $(this).prop('checked'));
            });
        }

        function initializeSelectedProducts() { // Asegurar que las tarjetas con checkboxes marcados tengan la clase 'border-primary-dark'
            $('.product-checkbox:checked').each(function() {
                var card = $(this).closest('.product-card');
                if (!card.hasClass('border-primary-dark')) {
                    card.addClass('border-primary-dark text-warning-dark bg-warning-light');
                }
            });
        }

        function toggleCardSelection(card, isSelected) { // Alternar la selección de una tarjeta
            if (isSelected) {
                card.addClass('border-success-dark text-warning-dark bg-warning-light');
            } else {
                card.removeClass('border-success-dark text-warning-dark bg-warning-light');
            }
            updateSaveButton();
        }

        function updateSaveButton() { // Actualizar inputs ocultos para enviar como array
            var selectedIds = [];
            $('.product-checkbox:checked').each(function() {
                selectedIds.push($(this).data('producto-id'));
            });
            var container = $('#selectedProductsContainer');
            container.empty();
            if (selectedIds.length > 0) {
                selectedIds.forEach(function(id) {
                    container.append('<input type="hidden" name="productos[]" value="' + id + '">');
                });
                $('#saveProductsBtn').prop('disabled', false).html('<i class="bx bx-save"></i> Guardar (' + selectedIds.length + ')');
            } else {
                $('#saveProductsBtn').prop('disabled', false).html('<i class="bx bx-save"></i> Guardar');
            }
        }
        if (!$('.zero-configuration').hasClass('dataTable')) { // Inicializar listeners y estado inicial // Si DataTables no se inicializó, ejecutar directamente
            initializeSelectedProducts();
            attachCardListeners();
            updateSaveButton();
        }
        $('#saveProductsBtn').on('click', function(e) { // Guardar asignaciones
            e.preventDefault();
            var btn = $(this);
            var originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Guardando...');
            $('#assignProductsForm').submit();
        });
    });

</script>
@stop
