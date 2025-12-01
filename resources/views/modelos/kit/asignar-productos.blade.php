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

    /* ELIMINAR LINEAS DE DIVISION ENTRE FILAS */
    #datatable tbody tr {
        border: none !important;
        border-bottom: none !important;
    }

    #datatable tbody tr td {
        border: none !important;
        border-bottom: none !important;
        border-top: none !important;
    }

    #datatable thead tr th {
        border: none !important;
    }

    #datatable.table td,
    #datatable.table th {
        border-top: none !important;
        border-bottom: none !important;
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

<form id="assignProductsForm" action="{{ route('kit.sincronizar-productos', $kit->id) }}" method="POST" style="display: none;">
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
        // VARIABLES GLOBALES
        var kitId = {{ $kit->id }};
        var storageKey = 'selectedProducts_' + kitId;
        var initialKitProductosIds = @json($kitProductosIds);

        // FUNCIONES DE GESTION DE SESSIONSTORAGE
        function getSelectedProducts() {
            var stored = sessionStorage.getItem(storageKey);
            return stored ? JSON.parse(stored) : [];
        }

        function saveSelectedProducts(ids) {
            sessionStorage.setItem(storageKey, JSON.stringify(ids));
        }

        function addSelectedProduct(productId) {
            var selectedIds = getSelectedProducts();
            if (selectedIds.indexOf(productId) === -1) {
                selectedIds.push(productId);
                saveSelectedProducts(selectedIds);
            }
        }

        function removeSelectedProduct(productId) {
            var selectedIds = getSelectedProducts();
            var index = selectedIds.indexOf(productId);
            if (index > -1) {
                selectedIds.splice(index, 1);
                saveSelectedProducts(selectedIds);
            }
        }

        // INICIALIZACION DE SESSIONSTORAGE
        function initializeStorage() {
            sessionStorage.removeItem(storageKey); // Siempre limpiar sessionStorage al cargar la página para evitar datos obsoletos
            if (initialKitProductosIds.length > 0) { // Inicializar solo con los productos que vienen de la base de datos
                saveSelectedProducts(initialKitProductosIds);
            }
        }
        initializeStorage();

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
                    attachCardListeners();
                    initializeSelectedProducts();
                    updateSaveButton();
                }
                , "initComplete": function(settings, json) {
                    initializeSelectedProducts();
                    attachCardListeners();
                    updateSaveButton();
                }
            });
        }

        // FUNCIONES PARA MANEJAR LA SELECCION DE PRODUCTOS
        function attachCardListeners() {
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

        function initializeSelectedProducts() {
            var selectedIds = getSelectedProducts();
            $('.product-checkbox').each(function() {
                var productId = $(this).data('producto-id');
                var isSelected = selectedIds.indexOf(productId) > -1;
                $(this).prop('checked', isSelected);
                var card = $(this).closest('.product-card');
                if (isSelected) {
                    if (!card.hasClass('border-primary-dark')) {
                        card.addClass('border-primary-dark text-warning-dark bg-warning-light');
                    }
                } else {
                    card.removeClass('border-primary-dark text-warning-dark bg-warning-light');
                }
            });
        }

        function toggleCardSelection(card, isSelected) {
            var productId = card.data('producto-id');
            if (isSelected) {
                card.addClass('border-success-dark text-warning-dark bg-warning-light');
                addSelectedProduct(productId);
            } else {
                card.removeClass('border-success-dark text-warning-dark bg-warning-light');
                removeSelectedProduct(productId);
            }
            updateSaveButton();
        }

        function updateSaveButton() {
            var selectedIds = getSelectedProducts();
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
        if (!$('.zero-configuration').hasClass('dataTable')) {
            initializeSelectedProducts();
            attachCardListeners();
            updateSaveButton();
        }
        $('#saveProductsBtn').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            var originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Guardando...');
            $('#assignProductsForm').submit();
        });
    });

</script>
@stop
