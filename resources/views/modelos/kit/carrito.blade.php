@extends('servicios')

@section('header') 
    @php //Calculo Temporal
        $total = 0;
        $atencionActual = $atencion->first();
        if ($atencionActual && $atencionActual->ordenes) {
            $total = $atencionActual->ordenes->sum(function($orden) {
                return $orden->precio * $orden->unidades;
            });
        }
    @endphp
    <div class="row align-items-center flex-nowrap">
        <div class="col-auto d-flex justify-content-start">
            <a href="{{ route('tienda') }}" class="btn btn-primary-light">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <div class="col d-flex justify-content-center">
            <span style="font-size: 1.5em;">ORDEN # {{ $atencion_id_ripped }} / {{ $atencion->first()->id }}</span>
        </div>
        <div class="col-auto d-flex justify-content-end">
            <span style="font-size: 1.9em;"><i class="fas fa-cart-plus" style="padding-right: 0.5em;"></i>${{ number_format($total, 2) }}</span>
        </div>
    </div>
@endsection

@push('css')
<style>
    .marcador_fila_par {
        border-right: 0.3em solid #3676ed;
    }

    .marcador_fila_impar {
        border-right: 0.2em solid #babbbbff;
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

    .no-spinners::-webkit-outer-spin-button, /* Chrome, Safari, Edge, Opera */
    .no-spinners::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    .no-spinners { /* Firefox */
      -moz-appearance: textfield;
    }

    .btn-scale-hover {
        transition: transform 0.2s ease;
    }
    
    .btn-scale-hover:hover {
        transform: scale(1.2);
    }
    
    .btn-scale-hover:active {
        transform: scale(0.9);
    }
</style>
@endpush

@section('content')
@php
    $currentAtencion = $atencion->first();
    $hasOrders = $currentAtencion && $currentAtencion->ordenes && $currentAtencion->ordenes->count() > 0;
@endphp

@if(!$hasOrders)
    <div class="d-flex flex-column justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-shopping-basket text-light-primary" style="font-size: 5rem; opacity: 0.5;"></i>
            </div>
            <h3 class="text-muted fw-light">Aún no has agregado articulos al carrito</h3>
            <p class="text-muted mb-4">Explora nuestros productos.</p>
            <a href="{{ route('tienda') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                <i class="fas fa-store me-2"></i> Ir a la tienda
            </a>
        </div>
    </div>
@else
    @if($currentAtencion && $currentAtencion->ordenes)
        @foreach($currentAtencion->ordenes as $orden)
            @php $headingId = 'heading' . $orden->id; $accordionId = 'accordion' . $orden->id; $ordenIndex = $loop->index; @endphp
            <div class="row mb-1 py-2 align-items-center"> <!--Kits-->
                <div class="col-12 col-md-8 mb-2 {{ $loop->index % 2 == 0 ? 'marcador_fila_par' : 'marcador_fila_impar' }}">
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
                                                    <button class="accordion-button collapsed d-flex justify-content-start text-start" style="padding: 0.5em; font-size: 0.8rem;" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $detCollapseId }}" aria-expanded="false" aria-controls="{{ $detCollapseId }}">


                                                        <a href="#" role="button"
                                                            data-html="true"
                                                            data-placement="bottom"
                                                            class="btn btn-scale-hover align-center text-danger-dark">
                                                            <i class="fas fa-trash" style="font-size: 0.85
rem;"></i>
                                                        </a>


                                                        <span class="badge bg-secondary me-2">{{ $detalle->unidades }}</span>
                                                        <span id="badgeId_{{ $detAccordionId }}" class="badge bg-secondary-dark me-2">{{ $detalle->producto_id }}</span>
                                                        <span id="productName_{{ $detAccordionId }}">{{ $detalle->producto->producto }}</span>
                                                        <input type="hidden" id="productId_{{ $detAccordionId }}" value="{{ $detalle->producto_id }}">
                                                    </button>
                                                </h2>
                                                <div id="{{ $detCollapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $detHeadingId }}" data-bs-parent="#{{ $detAccordionId }}">
                                                    <div class="accordion-body"> {{-- Equivalentes --}}
                                                        @php
                                                            $kitProducto = $detalle->producto->kitProductos->where('kit_id', $orden->kit_id)->first();
                                                        @endphp
                                                        @if($kitProducto)
                                                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2">
                                                                @if ($kitProducto->equivalentes->count() > 0)
                                                                    <div class="col">
                                                                        <label class="card rounded border m-0 shadow-none h-100" style="cursor: pointer;">
                                                                            <div class="card-header bg-secondary-dark text-white text-center p-1">
                                                                                <small class="fw-bold">{{ $kitProducto->producto->id }}</small>
                                                                            </div>
                                                                            <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                                <div class="mb-2">
                                                                                    <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $kitProducto->producto->id }}" data-name-target="#productName_{{ $detAccordionId }}" data-id-target="#productId_{{ $detAccordionId }}" data-badge-target="#badgeId_{{ $detAccordionId }}" data-product-name="{{ $kitProducto->producto->producto }}" {{ $detalle->producto_id == $kitProducto->producto->id ? 'checked' : '' }} onchange="updateProductName(this)">
                                                                                </div>
                                                                                <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                                    <span class="d-block">{{ $kitProducto->producto->producto }}</span>
                                                                                    <span class="badge badge-primary badge-pill mt-1 mx-auto">Estándar</span>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                                @foreach($kitProducto->equivalentes as $equivalente) 
                                                                    <div class="col">
                                                                        <label class="card rounded border m-0 shadow-none h-100" style="cursor: pointer;">
                                                                            <div class="card-header bg-secondary-dark text-white text-center p-1">
                                                                                <small class="fw-bold">{{ $equivalente->producto->id }}</small>
                                                                            </div>
                                                                            <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                                <div class="mb-2">
                                                                                    <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $equivalente->producto->id }}" data-name-target="#productName_{{ $detAccordionId }}" data-id-target="#productId_{{ $detAccordionId }}" data-badge-target="#badgeId_{{ $detAccordionId }}" data-product-name="{{ $equivalente->producto->producto }}" {{ $detalle->producto_id == $equivalente->producto->id ? 'checked' : '' }} onchange="updateProductName(this)">
                                                                                </div>
                                                                                <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                                    {{ $equivalente->producto->producto }}
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
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
                <div class="col-6 col-md-2 d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-primary-light shadow-sm rounded-circle d-flex align-items-center justify-content-center p-0 btn-scale-hover btn-spinner" 
                        data-type="minus" data-target="#unidades_{{ $orden->id }}" data-step="1"
                        style="width: 1.5rem; height: 1.5rem; cursor: pointer;">
                        <i class="fas fa-minus text-danger" style="font-size: 0.9rem;"></i>
                    </button>
                    <div class="d-flex flex-column align-items-center justify-content-center mx-2" style="width: 4.5rem;">
                        <input id="unidades_{{ $orden->id }}" type="number" min="1" step="1"
                            class="form-control text-center no-spinners input-unidades {{ $errors->has('ordenes.' . $orden->id) ? 'is-invalid' : '' }}" 
                            name="unidades" data-orden-id="{{ $orden->id }}"
                            aria-label="unidades" value="{{ old('ordenes.' . $orden->id, $orden->unidades) }}" 
                            required
                            style="width: 100%;">
                        <div class="invalid-feedback">
                            Solo números positivos (mínimo 1)
                        </div>
                        @error('ordenes.' . $orden->id)
                            <div class="badge bg-danger text-wrap" style="margin-top: 0.2rem; font-size: 0.7rem;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <button type="button" class="btn btn-primary-light shadow-sm rounded-circle d-flex align-items-center justify-content-center p-0 btn-scale-hover btn-spinner" 
                        data-type="plus" data-target="#unidades_{{ $orden->id }}" data-step="1"
                        style="width: 1.5rem; height: 1.5rem; cursor: pointer;">
                        <i class="fas fa-plus text-success" style="font-size: 0.9rem;"></i>
                    </button>
                </div>
                <div class="col-3 col-md-1 text-center d-flex align-items-center justify-content-center">
                    ${{ number_format($orden->precio, 2) }}
                </div>
                <div class="col-3 col-md-1 text-center d-flex align-items-center justify-content-center">
                    <a href="#" role="button"
                        data-html="true"
                        data-placement="bottom"
                        class="btn btn-scale-hover align-center text-danger-dark">
                        <i class="fas fa-trash" style="font-size: 1rem;"></i>
                    </a>
                </div>
            </div>
        @endforeach

        <div class="row mt-4">
            <div class="col-12 col-md-12 d-flex justify-content-end">
                <button type="button" id="btnEnviarCarrito" class="btn btn-primary">
                    Enviar
                </button>
            </div>
        </div>

    @endif
@endif
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
    window.cartDetails = { ordenes: [] };
    @if($hasOrders)
        @foreach($currentAtencion->ordenes as $orden)
            window.cartDetails.ordenes[{{ $loop->index }}] = {
                atencion_id: '{{ $orden->atencion_id }}',
                orden_id: '{{ $orden->id }}',
                kit_id: '{{ $orden->kit_id }}',
                unidades: {{ $orden->unidades }},
                detalles: [
                    @foreach($orden->detalle as $detalle)
                        {
                            producto_id: '{{ $detalle->producto_id }}'
                        }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            };
        @endforeach
    @endif
    $(document).ready(function() { 
        $('.main-kit-collapse').on('show.bs.collapse hidden.bs.collapse', function (e) { // Accordion Logic (Sync row height)
            if (e.target === this) {
                const isShowing = e.type === 'show';
                $(this).closest('.row')
                    .toggleClass('align-items-center', !isShowing)
                    .toggleClass('align-items-stretch', isShowing);
            }
        });
        const validate = ($el) => $el.toggleClass('is-invalid', !$el[0].checkValidity()); // Frontend Validation (Compact Standard)
        $(document).on('input blur', '.input-unidades', function() { validate($(this)); });
        $('#btnEnviarCarrito').on('click', function(e) { // Submit Handler
            let isValid = true;
            $('.input-unidades').each(function() {
                validate($(this));
                if (!$(this)[0].checkValidity()) isValid = false;
            });
            if (!isValid) return false; // Stop if invalid (HTML5 valid msg is shown below input)
            const $btn = $(this);
            $('.input-unidades').each(function() { // Actualizar unidades en la estructura jerárquica
                const ordenId = String($(this).data('orden-id'));
                for (let key in window.cartDetails.ordenes) {
                    if (window.cartDetails.ordenes[key].orden_id && String(window.cartDetails.ordenes[key].orden_id) === ordenId) {
                        window.cartDetails.ordenes[key].unidades = parseInt($(this).val());
                         if(window.cartDetails.ordenes[key].detalles) {
                            window.cartDetails.ordenes[key].detalles.forEach((det, idx) => {
                                const inputId = `#productId_accordion_det_${ordenId}_${idx}`;
                                const val = $(inputId).val();
                                if(val) {
                                    det.producto_id = val;
                                }
                            });
                        }
                        break;
                    }
                }
            });
            $btn.prop('disabled', true); //Procesode envío de la orden de compras
            $.ajax({
                url: '{{ route('tienda.carrito-enviar') }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    _token: '{{ csrf_token() }}',
                    cart: window.cartDetails
                }),
                success: function(response) {
                    $btn.prop('disabled', false);
                    toastr.success(response.message, null, { "progressBar": false, "timeOut": 0, "extendedTimeOut": 0 });
                    setTimeout(function() {
                        window.location.href = "{{ route('recepcion.solicitudes') }}";
                    }, 2000);
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    const errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                        ? xhr.responseJSON.message 
                        : 'Error al procesar la orden';
                    toastr.error(errorMessage, null, { "progressBar": true, "timeOut": 10000, "extendedTimeOut": 5000 });
                }
            });
        });
        $(document).on('click', '.btn-spinner', function() { // Spinner Logic
            const $btn = $(this), $input = $($btn.data('target'));
            let val = parseInt($input.val()) || 0;
            $input.val(Math.max(1, val + ($btn.data('type') === 'plus' ? 1 : -1))).trigger('change');
        });
    });
    function updateProductName(radio) { // Update product name and ID in specific elements
        const productName = radio.getAttribute('data-product-name');
        const targetSelector = radio.getAttribute('data-name-target');
        const idSelector = radio.getAttribute('data-id-target');
        const badgeSelector = radio.getAttribute('data-badge-target');
        const productId = radio.value;
        if (targetSelector && productName) {  // Actualizar Nombre Visual
             const targetElement = document.querySelector(targetSelector);
             if (targetElement) {
                 targetElement.textContent = productName;
             }
        }
        if (idSelector && productId) { // Actualizar ID en Hidden Input
            const idElement = document.querySelector(idSelector);
            if (idElement) {
                idElement.value = productId;
            }
        }
        if (badgeSelector && productId) { // Actualizar Badge Visual de ID
            const badgeElement = document.querySelector(badgeSelector);
            if (badgeElement) {
                badgeElement.textContent = productId;
            }
        }
    }
</script>
@endpush
