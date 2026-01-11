@extends('servicios')

@section('header')
    <div class="row align-items-center flex-nowrap">
        <div class="col-auto d-flex justify-content-start">
            <a href="{{ route('dashboard') }}" class="btn btn-primary-light text-primary-dark">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="col d-flex justify-content-center">
            <spam style="font-size: 1rem;">... EL SALVADOR</spam>
        </div>
        <div class="col-auto d-flex justify-content-end">
            <a href="{{ route('tienda.carrito') }}" class="text-primary-light position-relative" id="btn-carrito">
                <i class="fas fa-cart-plus fa-2x"></i>
                <span id="badge-carrito" class="badge-carrito-count d-none">0</span>
            </a>
        </div>
    </div>
@endsection

@push('css')
@endpush

@section('content')
{{-- GALERIA DE KITS --}}
<div class="d-flex flex-column h-100">
    <div class="row">
        <div class="col-lg-12">
            <div class="row mb-3"> {{-- Categorías --}}
                <div class="col-md-4 ms-auto">
                    <div class="d-flex justify-content-end">
                        <input class="form-control form-control-md" type="text" placeholder="Buscar producto" aria-label=".form-control-lg example">
                    </div>
                </div>
            </div>
            <div class="row" id="contenedor-kits"> {{-- Tienda --}}
                @foreach ($kits as $kit)
                <div class="col-md-3 kit-item" data-kit-name="{{ strtolower($kit->kit) }}">
                    <div class="card mb-4" style="border: 0;">
                        <div class="card">
                            <img class="card-img img-fluid" src="{{ ($kit->image_path && Storage::disk('public')->exists($kit->image_path)) ? Storage::disk('public')->url($kit->image_path) : asset('app-assets/images/pages/mercaderia.png') }}">
                            <div class="card-img-overlay product-overlay d-flex align-items-start justify-content-end" style="pointer-events: none;">
                                <ul class="list-unstyled">
                                    <li><a class="btn btn-success text-white" href="#" style="pointer-events: auto;"><i class="far fa-heart"></i></a></li>
                                    <li><a class="btn btn-success text-white mt-2 btn-ver-kit" data-bs-toggle="modal" data-bs-target="#modalPreferencias" data-kit-id="{{ $kit->id }}" data-kit-name="{{ $kit->kit }}" data-kit-image-path="{{ $kit->image_path ? Storage::url($kit->image_path) : '' }}" href="#" style="pointer-events: auto;"><i class="far fa-eye"></i></a></li>
                                    <li><a class="btn btn-success text-white mt-2 btn-agregar-kit" href="{{ Route('tienda.agregar-kit', $kit->id) }}" style="pointer-events: auto;"><i class="fas fa-cart-plus"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body ">
                            <a href="{{ Route('tienda.agregar-kit', $kit->id) }}" class="text-decoration-none d-flex justify-content-center btn-agregar-kit">{{ $kit->kit }}</a>
                            <ul class="w-100 list-unstyled d-flex justify-content-between mb-0">
                                <li class="pt-2">
                                    <span class="product-color-dot color-dot-red float-left rounded-circle ml-1"></span>
                                    <span class="product-color-dot color-dot-blue float-left rounded-circle ml-1"></span>
                                    <span class="product-color-dot color-dot-black float-left rounded-circle ml-1"></span>
                                    <span class="product-color-dot color-dot-light float-left rounded-circle ml-1"></span>
                                    <span class="product-color-dot color-dot-green float-left rounded-circle ml-1"></span>
                                </li>
                            </ul>
                            <ul class="list-unstyled d-flex justify-content-center mb-1">
                                <li>
                                    <i class="text-warning fa fa-star"></i>
                                    <i class="text-warning fa fa-star"></i>
                                    <i class="text-warning fa fa-star"></i>
                                    <i class="text-muted fa fa-star"></i>
                                    <i class="text-muted fa fa-star"></i>
                                </li>
                            </ul>
                            <p class="text-center mb-0">${{ number_format($kit->precio, 2) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="row mt-auto mb-3">
        <div class="col-12" id="paginacion-container">
            {{-- Paginación inyectada por JS --}}
        </div>
    </div>
</div>
{{-- MODAL KIT --}}
<div class="modal fade" id="modalPreferencias" tabindex="-1" aria-labelledby="modalPreferenciasLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreferenciasLabel">Estandares del Kit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"> {{-- Imagen del Kit --}}
                            <div id="kit-image-container" class="d-flex align-items-center justify-content-center h-100">
                                <img id="kit-image" src="" alt="Foto del Kit" class="img-fluid" style="display: none; max-height: 400px; object-fit: contain;">
                                <p id="kit-image-placeholder" class="text-muted">Foto del Kit</p>
                            </div>
                        </div>
                        <div class="col-sm-6"> {{-- Detalles Generales del Kit --}}
                            <div id="kit-details-container">
                                <h4 id="kit-name" class="mb-3"></h4>
                                <p>Detalles del Kit</p>
                            </div>
                        </div>
                    </div>
                    <div class="row"> {{-- Productos (Carrusel de alternativas) --}}
                        <div class="col-sm-12">
                            <div class="row" id="kit_productos">
                                {{-- dibujar aqui los productos del kit clicado --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modal-footer">
                {{-- Dibujar aqui el tablero de control --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<div class="row">
    <div class="col-12 col-md-6 text-center text-md-start">
        Texto a la izquierda del pie de página
    </div>
    <div class="col-12 col-md-6 text-center text-md-end">
        Texto a la derecha del pie de página
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalPreferencias = document.getElementById('modalPreferencias');
        const kitImageContainer = document.getElementById('kit-image-container');
        const kitImage = document.getElementById('kit-image');
        const kitImagePlaceholder = document.getElementById('kit-image-placeholder');
        modalPreferencias.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-ver-kit')) {
                const kitId = button.getAttribute('data-kit-id');
                const kitName = button.getAttribute('data-kit-name');
                const kitImagePath = button.getAttribute('data-kit-image-path');
                const kitNameElement = document.getElementById('kit-name');
                if (kitNameElement) { // Actualizar nombre del kit
                    kitNameElement.textContent = kitName || 'Nombre del Kit';
                }
                if (kitImagePath && kitImagePath.trim() !== '') { // Actualizar imagen del kit
                    kitImage.src = kitImagePath;
                    kitImage.style.display = 'block';
                    kitImagePlaceholder.style.display = 'none';
                    kitImage.onerror = function() {
                        this.style.display = 'none';
                        kitImagePlaceholder.style.display = 'block';
                        kitImagePlaceholder.textContent = 'Foto del Kit (no disponible)';
                    };
                } else {
                    kitImage.style.display = 'none';
                    kitImagePlaceholder.style.display = 'block';
                    kitImagePlaceholder.textContent = 'Foto del Kit (sin imagen)';
                }
                fetch('{{ route("tienda.get-kit-productos") }}', { // Cargar productos del kit via AJAX
                        method: 'POST'
                        , headers: {
                            'Content-Type': 'application/json'
                            , 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                        , body: JSON.stringify({
                            kit_id: kitId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const kitProductosContainer = document.getElementById('kit_productos');
                        const modalFooter = document.getElementById('modal-footer');
                        if (data.success) {
                            let agregarKitUrl = "{{ Route('tienda.agregar-kit', ':id') }}";
                            agregarKitUrl = agregarKitUrl.replace(':id', kitId);

                            let html = '';
                            data.kit.productos.forEach(producto => {
                                html += `
                                <div class="col-12 col-sm-3 p-2 d-flex flex-column align-items-center mb-2">
                                    <p class="text-center mb-2">${producto.producto}</p>
                                    <p class="badge bg-success mb-0">${producto.pivot.unidades}</p> <span class="text-muted small">Unidad(es)</span>
                                </div>
                            `;
                            });
                            modalFooter.innerHTML = `
                            <button type="button" class="btn btn-secondary" style="font-size: 0.8rem;" data-bs-dismiss="modal">Cerrar</button>
                            <a class="btn btn-secondary btn-agregar-kit" style="font-size: 1rem;" href="${agregarKitUrl}">
                                Agregar <i class="fas fa-cart-plus"></i>
                            </a>
                        `;
                            kitProductosContainer.innerHTML = html;
                        } else {
                            kitProductosContainer.innerHTML = '<p class="text-danger">Error al cargar productos</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('kit_productos').innerHTML = '<p class="text-danger">Error al cargar productos</p>';
                    });
            }
        });
        modalPreferencias.addEventListener('hide.bs.modal', function() {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });
        modalPreferencias.addEventListener('hidden.bs.modal', function() {
            kitImage.src = '';
            kitImage.style.display = 'none';
            kitImagePlaceholder.style.display = 'block';
            kitImagePlaceholder.textContent = 'Foto del Kit';
            const kitNameElement = document.getElementById('kit-name');
            if (kitNameElement) {
                kitNameElement.textContent = '';
            }
        });
        const updateCartBadge = () => { // Update Cart Badge Logic
            fetch('{{ route("tienda.cantidad") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('badge-carrito');
                    if (badge) {
                        badge.textContent = data.cantidad;
                        badge.classList.toggle('d-none', data.cantidad === 0);
                    }
                });
        };
        updateCartBadge();
        const handleAgregarKit = (e) => { // AJAX Add Kit Handler
            const href = e.currentTarget.getAttribute('href');
            if (href && href !== '#') {
                e.preventDefault();
                fetch(href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    toastr[data.type || (data.success ? 'success' : 'error')](data.message);
                    if (data.success) {
                        updateCartBadge();
                        // Cerrar modal si está abierta
                        const modalElement = document.getElementById('modalPreferencias');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error al procesar la solicitud');
                });
            }
        };
        $(document).on('click', '.btn-agregar-kit', handleAgregarKit); // Delegated for both gallery and modal
    });

    // LÓGICA DE PAGINACIÓN Y BÚSQUEDA FRONTEND
    const itemsPerPage = 2; // Ajusta este número según necesites
    let currentPage = 1;
    let allItems = [];
    let filteredItems = [];
    const initPagination = () => {
        allItems = Array.from(document.querySelectorAll('.kit-item'));
        filteredItems = [...allItems];
        renderPage();
    };
    const renderPage = () => {
        const totalItems = filteredItems.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        if (currentPage < 1) currentPage = 1; // Validar página actual
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        allItems.forEach(item => { // Mostrar/Ocultar items
            item.classList.add('d-none'); // Ocultar todos primero
        });
        filteredItems.slice(startIndex, endIndex).forEach(item => {
            item.classList.remove('d-none'); // Mostrar solo los de la página actual
        });
        renderPaginationControls(totalItems, totalPages, startIndex, endIndex);
    };
    const renderPaginationControls = (totalItems, totalPages, startIndex, endIndex) => {
        const container = document.getElementById('paginacion-container');
        if (!container) return;
        if (totalItems === 0) {
            container.innerHTML = '';
            return;
        }
        let html = `
            <nav class="d-flex justify-content-between align-items-center w-100">
                <div class="flex-grow-1">
                    <p class="text-muted mb-0">
                        Mostrando 
                        <span class="fw-bold">${Math.min(startIndex + 1, totalItems)}</span>
                        a
                        <span class="fw-bold">${Math.min(endIndex, totalItems)}</span>
                        de
                        <span class="fw-bold">${totalItems}</span>
                        resultados
                    </p>
                </div>
                <ul class="pagination pagination-lg mb-0">
        `;
        const prevDisabled = currentPage === 1; // Botón Anterior
        html += `
            <li class="page-item ${prevDisabled ? 'disabled' : ''}">
                <a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0" href="javascript:void(0)" ${!prevDisabled ? 'onclick="changePage(' + (currentPage - 1) + ')"' : ''}>&lsaquo;</a>
            </li>
        `;
        const windowSize = 2; // Páginas a los lados
        let range = [];
        for (let i = 1; i <= totalPages; i++) {
             if (i === 1 || i === totalPages || (i >= currentPage - windowSize && i <= currentPage + windowSize)) {
                 range.push(i);
             }
        }
        let prev = 0;
        range.forEach(i => {
            if (prev > 0 && i - prev !== 1) {
                html += `<li class="page-item disabled"><a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0" href="javascript:void(0)">...</a></li>`;
            }
            const active = i === currentPage;
            html += `
                <li class="page-item ${active ? 'active' : ''}">
                    <a class="page-link rounded-0 me-3 shadow-sm border-top-0 border-start-0 ${active ? '' : 'text-dark'}" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
                </li>
            `;
            prev = i;
        });
        const nextDisabled = currentPage === totalPages; // Botón Siguiente
        html += `
            <li class="page-item ${nextDisabled ? 'disabled' : ''}">
                <a class="page-link rounded-0 shadow-sm border-top-0 border-start-0 ${nextDisabled ? '' : 'text-dark'}" href="javascript:void(0)" ${!nextDisabled ? 'onclick="changePage(' + (currentPage + 1) + ')"' : ''}>&rsaquo;</a>
            </li>
        `;
        html += `</ul></nav>`;
        container.innerHTML = html;
    };
    window.changePage = (page) => { // Función global para los onclick
        currentPage = page;
        renderPage();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
    initPagination(); // Inicializar
    const searchInput = document.querySelector('input[placeholder="Buscar producto"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            if (searchTerm === '') {
                filteredItems = [...allItems];
            } else {
                filteredItems = allItems.filter(item => {
                    const name = item.getAttribute('data-kit-name') || '';
                    return name.includes(searchTerm);
                });
            }
            currentPage = 1; // Reset a primera página al buscar
            renderPage();
        });
        searchInput.focus(); // Enfocar al cargar
    }
</script>
@endpush
