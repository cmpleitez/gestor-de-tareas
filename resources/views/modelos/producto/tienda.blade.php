@extends('servicios')

@push('css')
@endpush

@section('content')
{{-- GALERIA DE KITS --}}
<div class="d-flex flex-column h-100">
    <div class="row">
        <div class="col-lg-12">
            
            {{-- CATEGORIAS --}}
            <div class="row mb-3">
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
            
            {{-- PREFERENCIAS DEL CLIENTE --}}
            <div class="row mb-3">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPreferencias">
                        Preferencias del cliente
                    </button>
                </div>
            </div>

            {{-- GALERIA --}}
            <div class="row">
                @foreach ($kits as $kit)
                <div class="col-md-3">
                    <div class="card mb-4 product-wap rounded-0">
                        <div class="card rounded-0">
                            <img class="card-img rounded-0 img-fluid" src="{{ asset('app-assets/images/pages/operador.png') }}">
                            <div class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                                <ul class="list-unstyled">
                                    <li><a class="btn btn-success text-white" href="#"><i class="far fa-heart"></i></a></li>
                                    <li><a class="btn btn-success text-white mt-2" href="#"><i class="far fa-eye"></i></a>
                                    </li>
                                    <li><a class="btn btn-success text-white mt-2" href="{{ Route('tienda.agregar', $kit->id) }}"><i class="fas fa-cart-plus"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <a href="#" class="text-decoration-none">{{ $kit->kit }}</a>
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

                    <div class="row">
                        <div class="col-sm-6" style="background-color:rgb(255, 253, 160);">
                            Foto del Kit
                        </div>                        
                        <div class="col-sm-6" style="background-color:rgb(110, 180, 104);">
                            Detalles del Kit
                        </div>                        
                    </div>

                    <div class="row">
                        <div class="col-sm-12" style="background-color:rgb(253, 184, 184);">
                            <div class="row">
                                <div class="col-12 col-sm-3" style="background-color: #e0e0e0;">
                                    Productos (Carrusel de alternativas)
                                </div>
                                <div class="col-12 col-sm-3" style="background-color:rgb(199, 184, 184);">
                                    Productos (Carrusel de alternativas)
                                </div>
                                <div class="col-12 col-sm-3" style="background-color: #e0e0e0;">
                                    Productos (Carrusel de alternativas)
                                </div>
                                <div class="col-12 col-sm-3" style="background-color:rgb(199, 184, 184);">
                                    Productos (Carrusel de alternativas)
                                </div>
                                <div class="col-12 col-sm-3" style="background-color: #e0e0e0;">
                                    Productos (Carrusel de alternativas)
                                </div>

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

@push('scripts')
@endpush
