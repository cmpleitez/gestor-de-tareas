@extends('servicios')

@push('css')
@endpush

@section('content')
{{-- GALERIA DE KITS --}}
<div class="d-flex flex-column h-100">
    <div class="row">
        <div class="col-lg-12">
            <div class="row mb-3"> {{-- Categorías --}}
                <div class="col-md-6">
                    <ul class="list-inline shop-top-menu pb-3 pt-1">
                        <li class="list-inline-item">
                            <a class="h5 text-dark text-decoration-none mr-3" href="#">Categoría 1</a>
                        </li>
                        <li class="list-inline-item">
                            <a class="h5 text-dark text-decoration-none mr-3" href="#">Categoría 2</a>
                        </li>
                        <li class="list-inline-item">
                            <a class="h5 text-dark text-decoration-none" href="#">Categoría 3</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 ms-auto">
                    <div class="d-flex justify-content-end">
                        <input class="form-control form-control-md" type="text" placeholder="Buscar producto" aria-label=".form-control-lg example">
                    </div>
                </div>
            </div>
            <div class="row"> {{-- Galería --}}
                @foreach ($kits as $kit)
                <div class="col-md-3">
                    <div class="card mb-4" style="border: 0;">
                        <div class="card">
                            <a href="#" class="d-block btn-ver-kit text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPreferencias" data-kit-id="{{ $kit->id }}" data-kit-name="{{ $kit->kit }}" data-kit-image-path="{{ $kit->image_path ? Storage::url($kit->image_path) : '' }}" style="pointer-events: auto;">
                                <img class="card-img img-fluid" src="{{ Storage::disk('public')->url($kit->image_path) ? Storage::disk('public')->url($kit->image_path) : asset('app-assets/images/pages/mercaderia.png') }}">
                            </a>

                            <div class="card-img-overlay product-overlay d-flex align-items-center justify-content-center" style="pointer-events: none;">
                                <ul class="list-unstyled">
                                    <li><a class="btn btn-success text-white" href="#" style="pointer-events: auto;"><i class="far fa-heart"></i></a></li>
                                    <li><a class="btn btn-success text-white mt-2 btn-ver-kit" data-bs-toggle="modal" data-bs-target="#modalPreferencias" data-kit-id="{{ $kit->id }}" data-kit-name="{{ $kit->kit }}" data-kit-image-path="{{ $kit->image_path ? Storage::url($kit->image_path) : '' }}" href="#" style="pointer-events: auto;"><i class="far fa-eye"></i></a></li>
                                    <li><a class="btn btn-success text-white mt-2" href="{{ Route('tienda.agregar-kit', $kit->id) }}" style="pointer-events: auto;"><i class="fas fa-cart-plus"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body ">
                            <a href="{{ Route('tienda.agregar-kit', $kit->id) }}" class="text-decoration-none d-flex justify-content-center">{{ $kit->kit }}</a>
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
        <ul class="pagination pagination-lg justify-content-end">
            <li class="page-item disabled">
                <a class="page-link active rounded-0 mr-3 shadow-sm border-top-0 border-left-0" href="#" tabindex="-1">1</a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-0 mr-3 shadow-sm border-top-0 border-left-0 text-dark" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-0 shadow-sm border-top-0 border-left-0 text-dark" href="#">3</a>
            </li>
        </ul>
    </div>
</div>
{{-- PREFERENCIAS DEL CLIENTE --}}
<div class="modal fade" id="modalPreferencias" tabindex="-1" aria-labelledby="modalPreferenciasLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreferenciasLabel">Título de la Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row"> {{-- Imagen del Kit --}}
                        <div class="col-sm-6" style="background-color:rgb(255, 253, 160);">
                            <div id="kit-image-container" class="d-flex align-items-center justify-content-center h-100">
                                <img id="kit-image" src="" alt="Foto del Kit" class="img-fluid" style="display: none; max-height: 400px; object-fit: contain;">
                                <p id="kit-image-placeholder" class="text-muted">Foto del Kit</p>
                            </div>
                        </div>
                        <div class="col-sm-6" style="background-color:rgb(110, 180, 104);">
                            <div id="kit-details-container"> {{-- DetallesGenrales del Kit --}}
                                <h4 id="kit-name" class="mb-3"></h4>
                                <p>Detalles del Kit</p>
                            </div>
                        </div>
                    </div>
                    <div class="row"> {{-- Productos (Carrusel de alternativas) --}}
                        <div class="col-sm-12" style="background-color:rgb(253, 184, 184);">
                            <div class="row" id="kit_productos">
                                {{-- dibujar aqui los productos del kit clicado --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- FUNCIONALIDADES DINÁMICAS --}}
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
                    if (data.success) {
                        let html = '';
                        data.productos.forEach(producto => {
                            html += `<div class="col-12 col-sm-3 p-2"><p class="mb-0">${producto.producto}</p></div>`;
                        });
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
    });

</script>
@endpush
