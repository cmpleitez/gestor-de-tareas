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
            @if(auth()->user()->mainRole->name == 'receptor' || auth()->user()->mainRole->name == 'operador')
                <a href="javascript:history.back()" class="btn btn-primary-light">
                    <i class="fas fa-arrow-left"></i>
                </a>
            @endif
            @if(auth()->user()->mainRole->name == 'cliente')
                <a href="{{ route('tienda') }}" class="btn btn-primary-light">
                    <i class="fas fa-arrow-left"></i>
                </a>
            @endif
        </div>
        <div class="col d-flex justify-content-center">
            <span style="font-size: 1.5em;">ORDEN # {{ $atencion_id_ripped }}</span>
        </div>
        @if(auth()->user()->mainRole->name == 'cliente' || auth()->user()->mainRole->name == 'receptor')
            <div class="col-auto d-flex justify-content-end">
                <span style="font-size: 1.9em;"><i class="fas fa-cart-plus" style="padding-right: 0.5em;"></i><span id="total-global">${{ number_format($total, 2) }}</span></span>
            </div>
        @endif
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

    [id^="btn_retirar_"] {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    [id^="btn_retirar_"]:hover {
        transform: scale(1.1);
        filter: brightness(0.8);
    }
    
    .btn-scale-hover:hover {
        transform: scale(1.2);
    }
    
    .btn-scale-hover:active {
        transform: scale(0.9);
    }

    /* ===== ESTILOS ELEGANTES PARA CONTROL DE RADIO DE STOCK ===== */
    .btn-check-stock {
        position: absolute;
        clip: rect(0,0,0,0);
        pointer-events: none;
    }

    .btn-group-stock-status {
        display: flex;
        flex-direction: row;
        gap: 0.5rem;
        padding: 0;
        flex-wrap: nowrap;
        justify-content: flex-start;
    }

    .btn-stock-status {
        flex: 0 1 auto;
        width: auto;
        min-width: auto;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        border: 2px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .btn-stock-status i {
        font-size: 1rem;
        transition: transform 0.3s ease;
    }

    .btn-stock-status span {
        position: relative;
        z-index: 1;
    }

    /* Estado: Verificado (Success) */
    .btn-stock-verified {
        background-color: var(--color-success-light);
        color: var(--text-success-dark);
        border: 2px solid transparent; /* Asegurar base sin borde visible pero con espacio */
    }

    .btn-stock-verified:hover {
        border-color: var(--border-success-dark);
        transform: translateY(-2px);
        transform: translateY(-2px);
    }

    .btn-check-stock:checked + .btn-stock-verified {
        background-color: var(--color-success);
        color: #ffffff;
        border-color: transparent; /* Quitar borde al estar seleccionado */
        transform: scale(1.05);
        transform: scale(1.05);
    }

    .btn-check-stock:checked + .btn-stock-verified i {
        transform: scale(1.2) rotate(360deg);
    }

    .btn-stock-unavailable {
        background-color: var(--color-warning-light);
        color: var(--text-warning-dark);
        border: 2px solid transparent;
    }

    .btn-stock-unavailable:hover {
        border-color: var(--border-warning-dark);
        transform: translateY(-2px);
        transform: translateY(-2px);
    }

    .btn-check-stock:checked + .btn-stock-unavailable {
        background-color: var(--color-warning);
        color: #ffffff;
        border-color: transparent;
        transform: scale(1.05);
        transform: scale(1.05);
    }

    .btn-check-stock:checked + .btn-stock-unavailable i {
        transform: scale(1.2) rotate(360deg);
    }



    /* Efecto de brillo al hacer clic */
    .btn-stock-status::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-check-stock:checked + .btn-stock-status::before {
        width: 300px;
        height: 300px;
        opacity: 0;
    }

    /* Responsive para móviles */
    @media (max-width: 768px) {
        .btn-group-stock-status {
            flex-direction: row;
        }
        
        .btn-stock-status {
            font-size: 0.7rem;
            padding: 0.4rem 0.8rem;
        }
    }


</style>
@endpush

@section('content')

@php
    $currentAtencion = $atencion->first();
    $hasOrders = $currentAtencion && $currentAtencion->ordenes && $currentAtencion->ordenes->count() > 0;
@endphp

<div id="empty-cart-msg" class="{{ $hasOrders ? 'd-none' : '' }} d-flex flex-column justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-shopping-basket text-light-primary" style="font-size: 5rem; opacity: 0.5;"></i>
        </div>
        <h3 class="text-muted fw-light">Aún no has agregado articulos al carrito</h3>
        <p class="text-muted mb-4">Explora nuestros productos.</p>
        @if(auth()->user()->mainRole->name == 'cliente')
            <a href="{{ route('tienda') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                <i class="fas fa-store me-2"></i> Ir a la tienda
            </a>
        @endif
    </div>
</div>

<div id="orders-container" class="{{ !$hasOrders ? 'd-none' : '' }}">
    @if($hasOrders)
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
                                <div class="accordion-body"> <!--Items-->
                                    @foreach($orden->detalle as $index => $detalle)
                                        @php
                                            $detHeadingId = 'heading_det_' . $orden->id . '_' . $index;
                                            $detAccordionId = 'accordion_det_' . $orden->id . '_' . $index;
                                            $detCollapseId = 'collapse_det_' . $orden->id . '_' . $index;
                                        @endphp
                                        <div class="accordion accordion-flush" id="{{ $detAccordionId }}">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header d-flex align-items-center" id="{{ $detHeadingId }}">
                                                    @if(auth()->user()->mainRole->name == 'cliente' || auth()->user()->mainRole->name == 'receptor')
                                                        <div class="ps-2 pe-1">
                                                            <i id="btn_retirar_{{ $detalle->orden_id }}_{{ $detalle->kit_id }}_{{ $detalle->producto_id }}"
                                                                class="fas fa-trash text-danger-dark" 
                                                                onclick="retirarItemAJAX(this)"
                                                                data-orden-id="{{ $detalle->orden_id }}"
                                                                data-kit-id="{{ $detalle->kit_id }}"
                                                                data-producto-id="{{ $detalle->producto_id }}"
                                                                data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="Eliminar producto">
                                                            </i>
                                                        </div>
                                                    @endif
                                                    <div class="w-100 d-flex flex-column gap-2 p-2">
                                                        {{-- Información del Producto --}}
                                                        <button class="d-flex justify-content-start text-start flex-grow-1 {{ (auth()->user()->mainRole->name == 'cliente' || auth()->user()->mainRole->name == 'receptor') ? 'accordion-button collapsed' : 'border-0 bg-transparent' }}" 
                                                            style="padding: 0.5em; font-size: 0.8rem;" 
                                                            type="button" 
                                                            @if(auth()->user()->mainRole->name == 'cliente' || auth()->user()->mainRole->name == 'receptor')
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#{{ $detCollapseId }}" 
                                                                aria-expanded="false" 
                                                                aria-controls="{{ $detCollapseId }}"
                                                            @endif>
                                                            <span class="text-secondary me-2">{{ $detalle->unidades }}</span>
                                                            <span id="badgeId_{{ $detAccordionId }}" class="badge bg-secondary-dark me-2">{{ $detalle->producto_id }}</span>
                                                            <span id="productName_{{ $detAccordionId }}">{{ $detalle->producto->producto }}</span>
                                                            <input type="hidden" id="productId_{{ $detAccordionId }}" value="{{ $detalle->producto_id }}">
                                                        </button>

                                                        {{-- Control de Radio para Estado de Stock Físico --}}
                                                        @if(auth()->user()->mainRole->name == 'operador')
                                                            <div class="btn-group-stock-status d-flex justify-content-end" role="group" aria-label="Estado de stock físico">
                                                                <input type="radio" 
                                                                    class="btn-check-stock" 
                                                                    name="stock_status_{{ $detAccordionId }}" 
                                                                    id="stock_verificado_{{ $detAccordionId }}" 
                                                                    value="1" 
                                                                    {{ $detalle->stock_fisico_existencias === true ? 'checked' : '' }}
                                                                    data-route="{{ route('recepcion.confirmar-stock') }}"
                                                                    data-orden-id="{{ $detalle->orden_id }}"
                                                                    data-kit-id="{{ $detalle->kit_id }}"
                                                                    data-producto-id="{{ $detalle->producto_id }}">
                                                                <label class="btn-stock-status btn-stock-verified" for="stock_verificado_{{ $detAccordionId }}">
                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                    <span>Existencias verificadas</span>
                                                                </label>

                                                                <input type="radio" 
                                                                    class="btn-check-stock" 
                                                                    name="stock_status_{{ $detAccordionId }}" 
                                                                    id="stock_sin_existencias_{{ $detAccordionId }}" 
                                                                    value="0" 
                                                                    {{ $detalle->stock_fisico_existencias === false ? 'checked' : '' }}
                                                                    data-route="{{ route('recepcion.confirmar-stock') }}"
                                                                    data-orden-id="{{ $detalle->orden_id }}"
                                                                    data-kit-id="{{ $detalle->kit_id }}"
                                                                    data-producto-id="{{ $detalle->producto_id }}">
                                                                <label class="btn-stock-status btn-stock-unavailable" for="stock_sin_existencias_{{ $detAccordionId }}">
                                                                    <i class="fas fa-times-circle me-1"></i>
                                                                    <span>No hay existencias</span>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </h2>
                                                @if(auth()->user()->mainRole->name == 'cliente' || auth()->user()->mainRole->name == 'receptor')
                                                    <div id="{{ $detCollapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $detHeadingId }}" data-bs-parent="#{{ $detAccordionId }}">
                                                        <div class="accordion-body"> {{-- Equivalentes --}}
                                                            @php
                                                                $kitProducto = $detalle->producto->kitProductos->where('kit_id', $orden->kit_id)->first();
                                                            @endphp
                                                            @if($kitProducto)
                                                            @if ($kitProducto->equivalentes->count() > 0)
                                                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2">
                                                                    <div class="col">
                                                                        <label class="card rounded border m-0 shadow-none h-100" style="cursor: pointer;">
                                                                            <div class="card-header text-center p-1">
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
                                                                    @foreach($kitProducto->equivalentes as $equivalente) 
                                                                        <div class="col">
                                                                            <label class="card rounded border m-0 shadow-none h-100" style="cursor: pointer;">
                                                                                <div class="card-header text-center p-1">
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
                                                                <div class="w-100 text-center">
                                                                    <small class="text-secondary" style="opacity: 0.50; font-size: 1rem;">Sin equivalentes asociados</small>
                                                                </div>
                                                            @endif
                                                            @else
                                                                <div class="alert alert-light mb-0 p-2">
                                                                    <small class="text-muted">No hay opciones disponibles o es un producto alternativo.</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->mainRole->name == 'receptor')
                    <div class="col-6 col-md-2 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-primary-light shadow-sm rounded-circle d-flex align-items-center justify-content-center p-0 btn-scale-hover btn-spinner" 
                            data-type="minus" data-target="#unidades_{{ $orden->id }}" data-step="1"
                            style="width: 1.5rem; height: 1.5rem; cursor: pointer;">
                            <i class="fas fa-minus text-danger" style="font-size: 0.9rem;"></i>
                        </button>
                        <div class="d-flex flex-column align-items-center justify-content-center mx-2" style="width: 4.5rem;">
                            <input id="unidades_{{ $orden->id }}" type="number" min="1" step="1"
                                class="form-control text-center no-spinners input-unidades {{ $errors->has('ordenes.' . $orden->id) ? 'is-invalid' : '' }}" 
                                name="unidades" data-orden-id="{{ $orden->id }}" data-precio="{{ $orden->precio }}"
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
                        <span id="subtotal_{{ $orden->id }}">${{ number_format($orden->precio * old('ordenes.' . $orden->id, $orden->unidades), 2) }}</span>
                    </div>
                    <div class="col-3 col-md-1 text-center d-flex align-items-center justify-content-center">
                        <i id="btn_retirar_orden_{{ $orden->id }}"
                            class="fas fa-trash text-danger-dark" 
                            onclick="retirarOrdenAJAX(this)"
                            data-url="{{ route('tienda.retirar-orden', $orden) }}"
                            data-orden-id="{{ $orden->id }}"
                            data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="Eliminar kit">
                        </i>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="row mt-4">
            <div class="col-12 col-md-12 d-flex justify-content-end">
                @if(auth()->user()->mainRole->name == 'cliente')
                    <button type="button" id="btnEnviarCarrito" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i> Enviar
                    </button>
                @endif
                @if(auth()->user()->mainRole->name == 'receptor')
                    <button type="button" id="darPorRevisado" class="btn btn-warning">
                        <i class="fas fa-check me-2"></i> Revisado
                    </button>
                @endif
                @if(auth()->user()->mainRole->name == 'operador')
                    <button type="button" 
                        id="btnConfirmarLoteStock" 
                        class="btn btn-primary"
                        data-atencion-id="{{ isset($orden) ? $orden->atencion_id : '' }}"
                        data-route="{{ route('recepcion.confirmar-stock') }}">
                        <i class="fas fa-clipboard-check me-2"></i> Revisado
                    </button>
                @endif
            </div>
        </div>

    @endif
@endif
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
    window.cartDetails = { ordenes: [] }; //Lectura de las ordenes de compras
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
        $('.main-kit-collapse').on('show.bs.collapse hidden.bs.collapse', function (e) { //Lógica del acordión y el alto de fila
            if (e.target === this) {
                const isShowing = e.type === 'show';
                $(this).closest('.row')
                    .toggleClass('align-items-center', !isShowing)
                    .toggleClass('align-items-stretch', isShowing);
            }
        });
        const validate = ($el) => $el.toggleClass('is-invalid', !$el[0].checkValidity()); //Validation
        $(document).on('input blur', '.input-unidades', function() { validate($(this)); });
        $('#btnEnviarCarrito').on('click', function(e) {
            let isValid = true;
            $('.input-unidades').each(function() {
                validate($(this));
                if (!$(this)[0].checkValidity()) isValid = false;
            });
            if (!isValid) return false;
            const $btn = $(this);
            $('.input-unidades').each(function() { //Unidades del Kit
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
            $btn.prop('disabled', true); //Proceso de envío
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
        $(document).on('click', '.btn-spinner', function() { // Lógica del spinner para cambio en las unidades
            const $btn = $(this), $input = $($btn.data('target'));
            let val = parseInt($input.val()) || 0;
            $input.val(Math.max(1, val + ($btn.data('type') === 'plus' ? 1 : -1))).trigger('change');
        });
        $(document).on('change keyup', '.input-unidades', function() { // Actualizar precio en tiempo real
            const ordenId = $(this).data('orden-id');
            const precio = parseFloat($(this).data('precio'));
            const unidades = parseInt($(this).val()) || 0;
            const subtotal = precio * unidades;
            const formatted = new Intl.NumberFormat('en-US', { // Formatear a moneda (USD standard)
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(subtotal);
            $('#subtotal_' + ordenId).text(formatted);
            let totalGlobal = 0; // Recalcular Total Global
            $('.input-unidades').each(function() {
                const p = parseFloat($(this).data('precio')) || 0;
                const u = parseInt($(this).val()) || 0;
                totalGlobal += p * u;
            });
            const formattedTotal = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(totalGlobal);
            $('#total-global').text(formattedTotal);
        });

    });

    // Retirar producto vía AJAX identificado por ID
    function retirarItemAJAX(elemento) {
        const btn = $(elemento);
        const elementoId = elemento.id; // Referencia al ID solicitada
        const ordenId = btn.data('orden-id');
        const kitId = btn.data('kit-id');
        const productoId = btn.data('producto-id');
        const accordionItem = btn.closest('.accordion-item');
        $.ajax({
            url: '{{ route("tienda.retirar-item") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                orden_id: ordenId,
                kit_id: kitId,
                producto_id: productoId
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.orden_vacia) {
                        // Si la orden quedó vacía, eliminar toda la fila de la orden con efecto suave
                        const orderRow = $('#accordion' + ordenId).closest('.row');
                        orderRow.fadeOut(400, function() {
                            $(this).remove();
                            // Verificar si quedan órdenes visibles en el contenedor
                            if ($('#orders-container .row').length === 0) {
                                $('#orders-container').fadeOut(400, function() {
                                    $('#empty-cart-msg').removeClass('d-none').hide().fadeIn();
                                });
                            }
                            // Recalcular totales
                            $('.input-unidades').first().trigger('input');
                        });
                    } else {
                        // Efecto visual solo para el item
                        accordionItem.fadeOut(400, function() {
                            $(this).remove();
                            // Recalcular totales después de eliminar item
                            $('.input-unidades').first().trigger('input');
                        });
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al retirar el producto';
                toastr.error(errorMsg);
            }
        });
    }

    // Retirar orden completa vía AJAX
    function retirarOrdenAJAX(elemento) {
        const btn = $(elemento);
        const elementoId = elemento.id; // Referencia al ID solicitada
        const url = btn.data('url');
        const ordenId = btn.data('orden-id');
        const orderRow = btn.closest('.row');

        $.ajax({
            url: url,
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Efecto visual para toda la fila de la orden
                    orderRow.fadeOut(400, function() {
                        $(this).remove();
                        // Verificar si el carrito quedó vacío
                        if ($('#orders-container .row').length === 0) {
                            $('#orders-container').fadeOut(400, function() {
                                $('#empty-cart-msg').removeClass('d-none').hide().fadeIn();
                            });
                        }
                        // Recalcular totales
                        $('.input-unidades').first().trigger('input');
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al retirar la orden';
                toastr.error(errorMsg);
            }
        });
    }
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

<script>
    // Confirmar stock por lote (Operador)
    $(document).on('click', '#btnConfirmarLoteStock', function() {
        const btn = $(this);
        const ruta = btn.data('route');
        const atencionId = btn.data('atencion-id');
        let stockData = [];
        let itemsSinConfirmar = [];
        let todoConfirmado = true;

        // Iterar sobre cada grupo de stock (un grupo por cada item/detalle)
        $('.btn-group-stock-status').each(function() {
            const grupo = $(this);
            const radioSeleccionado = grupo.find('input.btn-check-stock:checked');
            
            // Obtener datos del primer radio del grupo
            const primerRadio = grupo.find('input.btn-check-stock').first();
            const ordenId = primerRadio.data('orden-id');
            const kitId = primerRadio.data('kit-id');
            const productoId = primerRadio.data('producto-id');

            // Construir objeto con clave real de la BD
            let itemData = {
                orden_id: ordenId,
                kit_id: kitId,
                producto_id: productoId,
                stock_fisico_existencias: null
            };

            if (radioSeleccionado.length > 0) {
                itemData.stock_fisico_existencias = radioSeleccionado.val();
                stockData.push(itemData);
            } else {
                todoConfirmado = false;
                // Para feedback visual podríamos usar un identificador único si lo tuviéramos, 
                // o simplemente el mensaje genérico.
            }
        });

        // VALIDACIÓN FRONTEND: Debe haber algo seleccionado en todos los items
        if (!todoConfirmado) {
            toastr.error('Faltan ítems por confirmar. Por favor, revise todos los productos.');
            return;
        }

        // Envío AJAX
        $.ajax({
            url: ruta,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                atencion_id: atencionId,
                lote_stock: stockData
            },
            beforeSend: function() {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Procesando...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Opcional: Refrescar o redirigir según necesidad
                } else {
                    toastr.error(response.message || 'Error al procesar el lote');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error de conexión';
                toastr.error(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-clipboard-check me-2"></i> Revisado');
            }
        });
    });

    // Función antigua (mantener solo si es necesaria para otros roles, 
    // pero según el plan ya no se usa onchange)
    function confirmarStockAJAX(elemento) {
        // Obsoleto por cambio a confirmación por lote
    }
</script>
@endpush
