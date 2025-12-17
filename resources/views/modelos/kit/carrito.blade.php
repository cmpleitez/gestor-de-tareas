@extends('servicios')

@section('header')
    <div class="row align-items-center flex-nowrap">
        <div class="col-auto d-flex justify-content-start">
            <a href="{{ route('tienda') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="col d-flex justify-content-center">
            <span style="font-size: 1.2em;">ORDEN # {{ $atencion_id_ripped }}</span>
        </div>
        <div class="col-auto d-flex justify-content-end">
            <span style="font-size: 1.5em;">TOTAL: $ 9,999.99</span>
        </div>
    </div>
@endsection

@push('css')
<style>
    .marcador_fila_par {
        border-right: 0.3em solid #a6c6f5ff;
    }

    .marcador_fila_impar {
        border-right: 0.2em solid #d8dadbff;
    }

    .acordion-header {
        background-color: rgb(255, 255, 255) !important;
        min-height: 2em;
        font-size: 1.2em !important;
        padding-right: 3rem !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        position: relative;
        display: flex;
        align-items: center;
    }
    .accordion-button:not(.collapsed) {
        color: #0c63e4;
        background-color: transparent !important;
        box-shadow: none !important;
    }
    .accordion-button:focus {
        box-shadow: none !important;
        border-color: rgba(0,0,0,.125);
    }
    
    .accordion-flush .accordion-item, /* Eliminar bordes y sombras de los acordeones internos (flush) */
    .accordion-flush .accordion-header,
    .accordion-flush .accordion-button,
    .accordion-flush .accordion-collapse {
        border: none !important;
        box-shadow: none !important;
    }

    .accordion-button::after {
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
        margin-left: auto;
        content: "";
        background-repeat: no-repeat;
        background-size: 0.9em;
        transition: transform .2s ease-in-out;
    }

</style>
@endpush

@section('content')
@if($atencion->isEmpty())
    <div class="alert alert-info">
        Aún no ha agregado artículos al carrito
    </div>
@else
    <div class="row mb-2">
        <div class="col-12 col-md-9">
            <span>NOMBRE DEL KIT</span>
        </div>
        <div class="col-12 col-md-1 text-center">
            <span>UNIDADES</span>
        </div>
        <div class="col-12 col-md-1 text-center">
            <span>PRECIO</span>
        </div>
        <div class="col-12 col-md-1 text-center">
            <span>ACCIONES</span>
        </div>
    </div>
    @php $currentAtencion = $atencion->first(); @endphp <!--Kits-->
    @if($currentAtencion && $currentAtencion->ordenes)
        @foreach($currentAtencion->ordenes as $orden)
            @php $headingId = 'heading' . $orden->id; $accordionId = 'accordion' . $orden->id; @endphp
            <div class="row mb-1">
                <div class="col-12 col-md-9 {{ $loop->index % 2 == 0 ? 'marcador_fila_par' : 'marcador_fila_impar' }}">
                    <div class="accordion" id="{{ $accordionId }}">
                        <div class="accordion-item">
                            <span class="accordion-header" id="{{ $headingId }}">
                                <button class="accordion-button collapsed" style="padding: 0.5em; font-size: 0.8rem;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $orden->id }}" aria-expanded="false" aria-controls="collapse{{ $orden->id }}">
                                    {{ $orden->kit_id }} - {{ $orden->kit->kit }}
                                </button>
                            </span>
                            <div id="collapse{{ $orden->id }}" class="accordion-collapse collapse main-kit-collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#{{ $accordionId }}">
                                <div class="accordion-body"> <!--Productos-->
                                    @foreach($orden->detalle as $index => $detalle)
                                        @php
                                            $detHeadingId = 'heading_det_' . $orden->id . '_' . $index;
                                            $detAccordionId = 'accordion_det_' . $orden->id . '_' . $index;
                                            $detCollapseId = 'collapse_det_' . $orden->id . '_' . $index;
                                        @endphp
                                        <div class="accordion accordion-flush" id="{{ $detAccordionId }}">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="{{ $detHeadingId }}">
                                                    <button class="accordion-button collapsed" style="padding: 0.5em; font-size: 0.8rem;" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $detCollapseId }}" aria-expanded="false" aria-controls="{{ $detCollapseId }}">
                                                        {{ $detalle->producto->id }} - {{ $detalle->producto->producto }}
                                                    </button>
                                                </h2>
                                                <div id="{{ $detCollapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $detHeadingId }}" data-bs-parent="#{{ $detAccordionId }}">
                                                    <div class="accordion-body"> {{-- Equivalentes --}}
                                                        @php
                                                            $kitProducto = $detalle->producto->kitProductos->where('kit_id', $orden->kit_id)->first();
                                                        @endphp
                                                        @if($kitProducto)
                                                            <div class="d-flex flex-wrap">
                                                                <label class="card rounded border mb-2 shadow-none" style="width: 150px; margin-right: 10px; cursor: pointer;">
                                                                    <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                        <div class="mb-2">
                                                                            <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $kitProducto->producto->id }}" data-name-target="#productName_{{ $detAccordionId }}" data-product-name="{{ $kitProducto->producto->id }} - {{ $kitProducto->producto->producto }}" {{ $detalle->producto_id == $kitProducto->producto->id ? 'checked' : '' }} onchange="updateProductName(this)">
                                                                        </div>
                                                                        <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                            <span class="d-block">{{ $kitProducto->producto->id }} {{ $kitProducto->producto->producto }}</span>
                                                                            <span class="badge badge-info badge-pill mt-1">Estándar</span>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                @foreach($kitProducto->equivalentes as $equivalente) 
                                                                    <label class="card rounded border mb-2 shadow-none" style="width: 150px; margin-right: 10px; cursor: pointer;">
                                                                        <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                            <div class="mb-2">
                                                                                <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $equivalente->producto->id }}" data-name-target="#productName_{{ $detAccordionId }}" data-product-name="{{ $equivalente->producto->id }} - {{ $equivalente->producto->producto }}" {{ $detalle->producto_id == $equivalente->producto->id ? 'checked' : '' }} onchange="updateProductName(this)">
                                                                            </div>
                                                                            <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                                {{ $equivalente->producto->id }} - {{ $equivalente->producto->producto }}
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="alert alert-light mb-0 p-2">
                                                                <small class="text-muted">No hay opciones disponibles o es un producto alternativo.</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                             
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-1 text-center d-flex align-items-center justify-content-center">
                    {{ $orden->unidades }}
                </div>
                <div class="col-12 col-md-1 text-center d-flex align-items-center justify-content-center">
                    {{ $orden->precio }}
                </div>
                <div class="col-12 col-md-1 text-center d-flex align-items-center justify-content-center">
                    <a href="#" role="button"
                        data-html="true"
                        data-placement="bottom"
                        class="button_delete align-center border border-danger-dark text-danger-dark btn-danger-light">
                        <i class="fas fa-trash"></i>
                    </a>                
                </div>
            </div>
        @endforeach
    @endif
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() { // Toggle align-items-stretch on main accordion open/close to sync row height
        $('.main-kit-collapse').on('show.bs.collapse', function (e) {
            if (e.target === this) {
                $(this).closest('.row').removeClass('align-items-center').addClass('align-items-stretch');
            }
        });
        $('.main-kit-collapse').on('hidden.bs.collapse', function (e) {
            if (e.target === this) {
                $(this).closest('.row').removeClass('align-items-stretch').addClass('align-items-center');
            }
        });
    });
    function updateProductName(radio) { // Update product name in accordion header
        const productName = radio.getAttribute('data-product-name');
        const accordionItem = radio.closest('.accordion-item');

        if (accordionItem && productName) {
            const targetElement = accordionItem.querySelector('.accordion-button');
            if (targetElement) {
                targetElement.textContent = productName;
            }
        }
    }
</script>
@endpush
