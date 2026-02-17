@extends('servicios')

@section('header') 
    @php //Calculo Temporal
        $rol_usuario_actual = auth()->user()->mainRole->name;
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
            @if($rol_usuario_actual == 'receptor' || $rol_usuario_actual == 'operador')
                <a href="javascript:history.back()" class="btn btn-primary-light">
                    <i class="fas fa-arrow-left"></i>
                </a>
            @endif
            @if($rol_usuario_actual == 'cliente')
                <a href="{{ route('tienda') }}" class="btn btn-primary-light">
                    <i class="fas fa-arrow-left"></i>
                </a>
            @endif
        </div>
        <div class="col d-flex justify-content-center">
            <span style="font-size: 1.5em;">ORDEN # {{ $atencion_id_ripped }}</span>
        </div>
        @if($rol_usuario_actual == 'cliente' || $rol_usuario_actual == 'receptor')
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

    .accordion-flush .accordion-item, 
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

    .btn-stock-verified {
        background-color: var(--color-success-light);
        color: var(--text-success-dark);
        border: 2px solid transparent; 
    }

    .btn-stock-verified:hover {
        border-color: var(--border-success-dark);
        transform: translateY(-2px);
        transform: translateY(-2px);
    }

    .btn-check-stock:checked + .btn-stock-verified {
        background-color: var(--color-success);
        color: #ffffff;
        border-color: transparent; 
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
        @if($rol_usuario_actual == 'cliente')
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
                        <div class="accordion" id="{{ $accordionId }}" data-orden-id="{{ $orden->id }}">
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
                                                        @can('tienda')
                                                            @can('eliminar')
                                                                <div class="ps-2 pe-1">
                                                                    <i id="btn_retirar_{{ $detalle->orden_id }}_{{ $detalle->kit_id }}_{{ $detalle->producto_id }}"
                                                                        class="fas fa-trash text-danger-dark" 
                                                                        onclick="retirarItemAJAX(this)"
                                                                        data-orden-id="{{ $detalle->orden_id }}"
                                                                        data-kit-id="{{ $detalle->kit_id }}"
                                                                        data-producto-id="{{ $detalle->producto_id }}"
                                                                        data-popup="tooltip-custom" data-html="true" data-placement="bottom" title="Eliminar item">
                                                                    </i>
                                                                </div>
                                                            @endcan
                                                        @endcan
                                                        <div class="w-100 d-flex flex-column gap-2 p-2">
                                                            <button class="d-flex justify-content-start align-items-center text-start flex-grow-1 {{ ($rol_usuario_actual == 'cliente' || $rol_usuario_actual == 'receptor') ? 'accordion-button collapsed' : 'border-0 bg-transparent' }}" 
                                                                style="padding: 0.5em; font-size: 0.8rem;" 
                                                                type="button" 
                                                                data-orden-id="{{ $detalle->orden_id }}"
                                                                data-kit-id="{{ $detalle->kit_id }}"
                                                                @if($rol_usuario_actual == 'cliente' || $rol_usuario_actual == 'receptor')
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#{{ $detCollapseId }}" 
                                                                    aria-expanded="false" 
                                                                    aria-controls="{{ $detCollapseId }}"
                                                                @endif>
                                                                <span>{{ $detalle->unidades }}</span>
                                                                @if($rol_usuario_actual == 'receptor' || $rol_usuario_actual == 'operador') 
                                                                    @if(is_null($detalle->stock_fisico_existencias))
                                                                        <span class="px-1">
                                                                            <i class="fas fa-clock text-muted" title="Pendiente de revisión"></i>
                                                                        </span>
                                                                    @elseif($detalle->stock_fisico_existencias == 1)
                                                                        <span class="px-1">
                                                                            <i class="fas fa-check text-success" title="Stock verificado"></i>
                                                                        </span>
                                                                    @else
                                                                        <span class="px-1">
                                                                            <i class="fas fa-times text-danger" title="Sin stock"></i>
                                                                        </span>
                                                                    @endif
                                                                @endif
                                                                <div class="p-2">
                                                                    <p id="badgeId_{{ $detAccordionId }}" class="badge bg-secondary-dark text-white mb-1" style="font-size: 0.7rem;">{{ $detalle->producto->codigo ?? 'S/C' }}</p>
                                                                </div>

                                                                <span id="productName_{{ $detAccordionId }}">{{ $detalle->producto->producto }}</span>
                                                                <input type="hidden" id="productId_{{ $detAccordionId }}" value="{{ $detalle->producto_id }}" data-original-id="{{ $detalle->producto_id }}">
                                                            </button>
                                                            @if($rol_usuario_actual == 'operador')
                                                                <div class="btn-group-stock-status d-flex justify-content-end" role="group" aria-label="Estado de stock físico">
                                                                    <input type="radio" 
                                                                        class="btn-check-stock" 
                                                                        name="stock_status_{{ $detAccordionId }}" 
                                                                        id="stock_verificado_{{ $detAccordionId }}" 
                                                                        value="1" 
                                                                        {{ $detalle->stock_fisico_existencias === true ? 'checked' : '' }}
                                                                        data-route="{{ route('recepcion.revisar-stock') }}"
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
                                                                        data-route="{{ route('recepcion.revisar-stock') }}"
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
                                                    @if($rol_usuario_actual == 'cliente' || $rol_usuario_actual == 'receptor')
                                                        <div id="{{ $detCollapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $detHeadingId }}" data-bs-parent="#{{ $detAccordionId }}">
                                                            <div class="accordion-body"> {{-- Equivalentes --}}
                                                                @php
                                                                    $productoOriginalId = $detalle->producto_id_original ?? $detalle->producto_id;
                                                                    $productoOriginal = \App\Models\Producto::find($productoOriginalId);
                                                                    $kitProducto = $productoOriginal?->kitProductos->where('kit_id', $orden->kit_id)->first();
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
                                                                                            <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $kitProducto->producto->id }}" data-name-target="#productName_{{ $detAccordionId }}" data-id-target="#productId_{{ $detAccordionId }}" data-badge-target="#badgeId_{{ $detAccordionId }}" data-product-name="{{ $kitProducto->producto->producto }}" data-precio="{{ $kitProducto->producto->precio }}" data-es-estandar="true" {{ $detalle->producto_id == $kitProducto->producto->id ? 'checked' : '' }} onfocus="this.setAttribute('data-prev', this.checked ? this.value : '')" onchange="updateProductName(this)">
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
                                                                                    @php 
                                                                                        $stock = $equivalente->producto->oficinaStock->first()->unidades ?? 0;
                                                                                    @endphp 
                                                                                    <label class="card rounded border m-0 shadow-none h-100 {{ $stock == 0 ? 'bg-light' : '' }}" style="cursor: {{ $stock == 0 ? 'not-allowed' : 'pointer' }}; opacity: {{ $stock == 0 ? '0.5' : '1' }};">
                                                                                        <div class="card-header text-center p-1">
                                                                                            <small class="fw-bold">{{ $equivalente->producto->id }}</small>
                                                                                        </div>
                                                                                        <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                                            <div class="mb-2">
                                                                                                <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $equivalente->producto->id }}" data-name-target="#productName_{{ $detAccordionId }}" data-id-target="#productId_{{ $detAccordionId }}" data-badge-target="#badgeId_{{ $detAccordionId }}" data-product-name="{{ $equivalente->producto->producto }}" data-precio="{{ $equivalente->producto->precio }}" {{ $detalle->producto_id == $equivalente->producto->id ? 'checked' : '' }} {{ $stock == 0 ? 'disabled' : '' }} onfocus="this.setAttribute('data-prev', this.checked ? this.value : '')" onchange="updateProductName(this)">
                                                                                            </div>
                                                                                            <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                                                <span class="d-block">{{ $equivalente->producto->producto }}</span>
                                                                                                @if($stock >= 1 && $stock <= 3)
                                                                                                    <span class="badge bg-warning badge-pill mt-1 mx-auto">Stock {{ $stock }}</span>
                                                                                                @elseif($stock == 0)
                                                                                                    <span class="badge bg-secondary-light text-dark badge-pill mt-1 mx-auto">Sin stock</span>
                                                                                                @endif
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
                    @if($rol_usuario_actual == 'receptor' || $rol_usuario_actual == 'cliente' || $rol_usuario_actual == 'operador')
                        @if($rol_usuario_actual != 'operador')
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
                        @else
                            <div class="col-6 col-md-2 d-flex align-items-center justify-content-start">
                                <span class="fw-bold" style="margin-left: 1rem;">{{ $orden->unidades }} unidad(es)</span>
                            </div>
                        @endif
                        @if($rol_usuario_actual != 'operador')
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
                    @endif
                </div>
            @endforeach
            <div class="row d-flex justify-content-end mt-4"> {{--Tablero de control--}}
                <div class="col-12 col-md-12 d-flex justify-content-end gap-2">
                    @if($rol_usuario_actual == 'cliente')
                        <button type="button" id="btnEnviarCarrito" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i> Enviar
                        </button>
                    @endif
                    @if($rol_usuario_actual == 'receptor')
                        <button type="button" id="corregir-orden" class="btn btn-warning"
                            @if($atencion && $atencion->count() > 0)
                                data-atencion-id="{{ $atencion->first()->id }}"
                            @endif>
                            <i class="fas fa-pencil-alt"></i> Corregir
                        </button>
                    @endif
                    @if($rol_usuario_actual == 'receptor')
                        <button type="button" id="revisar-orden" class="btn btn-primary"
                            @if($atencion && $atencion->count() > 0)
                                data-atencion-id="{{ $atencion->first()->id }}"
                            @endif
                            data-recepcion-id="{{ $recepcion_id ?? '' }}"
                            data-route="{{ route('recepcion.revisar-orden') }}">
                            <i class="fas fa-clipboard-check me-2"></i> Revisar
                        </button>
                    @endif
                    @if($rol_usuario_actual == 'operador')
                        <button type="button" 
                            id="revisar-stock" 
                            class="btn btn-primary"
                            @if($atencion && $atencion->count() > 0)
                                data-atencion-id="{{ $atencion->first()->id }}"
                            @endif
                            data-recepcion-id="{{ $recepcion_id ?? '' }}"
                            data-route="{{ route('recepcion.revisar-stock') }}">
                            <i class="fas fa-clipboard-check me-2"></i> Revisar
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
            $btn.prop('disabled', true).html('<i class="fas fa-clock me-2"></i> Procesando...');
            $.ajax({
                url: '{{ route('tienda.carrito-enviar') }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    _token: '{{ csrf_token() }}',
                    cart: window.cartDetails
                }),
                success: function(response) {
                    toastr.success(response.message, null, { "progressBar": false, "timeOut": 0, "extendedTimeOut": 0 });
                    setTimeout(function() {
                        window.location.href = "{{ route('recepcion.solicitudes') }}";
                    }, 2000);
                },
                error: function(xhr) {
                    console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en carrito-enviar:", xhr);
                    $btn.prop('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i> Enviar');
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
            const formatted = new Intl.NumberFormat('en-US', { 
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
    function retirarItemAJAX(elemento) {
        const btn = $(elemento);
        const elementoId = elemento.id; 
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
                        const orderRow = $('#accordion' + ordenId).closest('.row');
                        orderRow.fadeOut(400, function() {
                            $(this).remove();
                            if ($('#orders-container').find('.row:has(.accordion)').length === 0) {
                                $('#orders-container').fadeOut(400, function() {
                                    $('#empty-cart-msg').removeClass('d-none').hide().fadeIn();
                                });
                            }
                            recalcularTotales();
                        });
                    } else {
                        accordionItem.fadeOut(400, function() {
                            $(this).remove();
                            if (response.nuevo_precio !== undefined) { 
                                const ordenInput = $(`.input-unidades[data-orden-id="${ordenId}"]`);
                                ordenInput.data('precio', response.nuevo_precio);
                                ordenInput.attr('data-precio', response.nuevo_precio);
                                const formattedSubtotal = new Intl.NumberFormat('en-US', {
                                    style: 'currency',
                                    currency: 'USD',
                                    minimumFractionDigits: 2
                                }).format(response.nuevo_subtotal);
                                $(`#subtotal_${ordenId}`).text(formattedSubtotal);
                            }
                            recalcularTotales();
                        });
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en retirarItemAJAX:", xhr);
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al retirar el producto';
                toastr.error(errorMsg);
            }
        });
    }
    function retirarOrdenAJAX(elemento) {
        const btn = $(elemento);
        const elementoId = elemento.id; 
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
                    orderRow.fadeOut(400, function() {
                        $(this).remove();
                        if ($('#orders-container').find('.row:has(.accordion)').length === 0) {
                            $('#orders-container').fadeOut(400, function() {
                                $('#empty-cart-msg').removeClass('d-none').hide().fadeIn();
                            });
                        }
                        recalcularTotales();
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en retirarOrdenAJAX:", xhr);
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al retirar la orden';
                toastr.error(errorMsg);
            }
        });
    }
    function updateProductName(radio) { 
        const productName = radio.getAttribute('data-product-name');
        const targetSelector = radio.getAttribute('data-name-target');
        const idSelector = radio.getAttribute('data-id-target');
        const badgeSelector = radio.getAttribute('data-badge-target');
        const productId = radio.value;
        if (targetSelector && productName) { 
            const targetElement = document.querySelector(targetSelector);
            if (targetElement) {
                targetElement.textContent = productName;
            }
        }
        if (idSelector && productId) { 
            const idElement = document.querySelector(idSelector);
            if (idElement) {
                idElement.value = productId;
            }
        }
        if (badgeSelector && productId) { 
            const badgeElement = document.querySelector(badgeSelector);
            if (badgeElement) {
                badgeElement.textContent = productId;
            }
        }
        const accordionContainer = $(radio).closest('.accordion[data-orden-id]');
        const ordenId = accordionContainer.data('orden-id');
        if (ordenId) {
            let nuevoPrecioKit = 0;
            let duplicadoEncontrado = false;
            let radiosSeleccionados = accordionContainer.find('input[type="radio"]:checked');
            let productosSeleccionados = [];
            radiosSeleccionados.each(function() {
                const val = $(this).val();
                if (productosSeleccionados.includes(val)) {
                    duplicadoEncontrado = true;
                    return false;
                }
                productosSeleccionados.push(val);
            });

            if (duplicadoEncontrado) {
                toastr.warning('Este producto ya forma parte del kit. Por favor selecciona otro.');
                const nombreGrupo = radio.name;
                const radioEstandar = $(`input[name="${nombreGrupo}"][data-es-estandar="true"]`);
                if (radioEstandar.length > 0) {
                    if (radioEstandar[0] !== radio) {
                        radioEstandar.prop('checked', true).trigger('change');
                    }
                } else {
                    $(radio).prop('checked', false); 
                }
                return;
            }
            radiosSeleccionados.each(function() {
                const precio = parseFloat($(this).data('precio'));
                if (!isNaN(precio)) {
                    nuevoPrecioKit += precio;
                }
            });
            const ordenInput = $(`.input-unidades[data-orden-id="${ordenId}"]`);
            if (ordenInput.length > 0) {
                ordenInput.data('precio', nuevoPrecioKit);
                ordenInput.attr('data-precio', nuevoPrecioKit);
                const unidades = parseInt(ordenInput.val()) || 0;
                const nuevoSubtotal = nuevoPrecioKit * unidades;
                const formattedSubtotal = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2
                }).format(nuevoSubtotal);
                $(`#subtotal_${ordenId}`).text(formattedSubtotal);
                recalcularTotales();
            }
        }
    }
    function recalcularTotales() { // Recalcular el total global después de eliminar items
        let totalGlobal = 0;
        $('.input-unidades').each(function() {
            const precio = parseFloat($(this).data('precio')) || 0;
            const unidades = parseInt($(this).val()) || 0;
            totalGlobal += precio * unidades;
        });
        const formattedTotal = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }).format(totalGlobal);
        $('#total-global').text(formattedTotal);
    }
    $(document).on('click', '#revisar-stock', function() {
        const btn = $(this);
        const ruta = btn.data('route');
        const atencionId = btn.data('atencion-id');
        const recepcionId = btn.data('recepcion-id');
        let stockData = [];
        let itemsSinConfirmar = [];
        let todoConfirmado = true;
        $('.btn-group-stock-status').each(function() {
            const grupo = $(this);
            const radioSeleccionado = grupo.find('input.btn-check-stock:checked');
            const primerRadio = grupo.find('input.btn-check-stock').first();
            const ordenId = primerRadio.data('orden-id');
            const kitId = primerRadio.data('kit-id');
            const productoId = primerRadio.data('producto-id');
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
            }
        });
        if (!todoConfirmado) {
            toastr.error('Faltan ítems por revisar.');
            return;
        }
        $.ajax({
            url: ruta,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                atencion_id: atencionId,
                recepcion_id: recepcionId,
                lote_stock: stockData
            },
            beforeSend: function() {
                btn.prop('disabled', true).html('<i class="fas fa-clock me-2"></i> Revisando...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.items_validados) {
                        response.items_validados.forEach(function(item) {
                            const $detalleContainer = $(`input[id^="productId_"][value="${item.producto_id}"]`)
                                .filter(function() {
                                    return true; 
                                })
                                .closest('.accordion-item');
                            const $stockRadio = $(`input.btn-check-stock[data-orden-id="${item.orden_id}"][data-kit-id="${item.kit_id}"][data-producto-id="${item.producto_id}"]`).first();
                            let $accordionHeader = $stockRadio.closest('.accordion-header');
                            if ($accordionHeader.length === 0) {
                                $accordionHeader = $stockRadio.closest('.accordion-collapse').prev('.accordion-header');
                            }
                            const $iconContainer = $accordionHeader.find('button .px-1');
                            $iconContainer.empty();
                            if (item.stock_existencias == "1") {
                                $iconContainer.html('<i class="fas fa-check text-success" title="Stock verificado"></i>');
                            } else {
                                $iconContainer.html('<i class="fas fa-times text-danger" title="Sin stock"></i>');
                            }
                        });
                    }
                    if (typeof cargarTareas === 'function') {
                        cargarTareas(recepcionId, atencionId);
                    }
                    setTimeout(function() {
                        window.location.href = "{{ route('recepcion.solicitudes') }}";
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Error al procesar el lote');
                }
            },
            error: function(xhr) {
                console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en revisar-stock click:", xhr);
                btn.prop('disabled', false).html('<i class="fas fa-clipboard-check"></i> Revisar');
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error de conexión';
                toastr.error(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Revisado');
            }
        });
    });
    $(document).on('click', '#revisar-orden', function() { // Revisar Orden (Receptor)
        const btn = $(this);
        const atencionId = btn.data('atencion-id');
        const recepcionId = btn.data('recepcion-id');
        const ruta = btn.data('route');
        let ordenes = [];
        $('.main-kit-collapse').each(function() {
            const collapseDiv = $(this);
            const ordenId = collapseDiv.attr('id').replace('collapse', '');
            const accordionButton = collapseDiv.prev().find('.accordion-button');
            const kitText = accordionButton.text().trim();
            const kitId = kitText.split(' -')[0].trim();
            const unidadesInput = $(`#unidades_${ordenId}`);
            const unidades = unidadesInput.val();
            if (!unidades || !ordenId || !kitId) return;
            let detalles = [];
            collapseDiv.find('.accordion.accordion-flush').each(function() {
                const detalleAccordion = $(this);
                const productIdInput = detalleAccordion.find(`input[id^="productId_"]`);
                const productoId = productIdInput.val();
                if (productoId) {
                    detalles.push({
                        kit_id: parseInt(kitId),
                        producto_id: productoId
                    });
                }
            });
            if (detalles.length > 0) {
                ordenes.push({
                    orden_id: parseInt(ordenId),
                    unidades: parseInt(unidades),
                    detalles: detalles
                });
            }
        });
        if (ordenes.length === 0) {
            toastr.error('No se encontraron órdenes para revisar.');
            return;
        }
        $.ajax({
            url: ruta,
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}', 
                atencion_id: atencionId, 
                recepcion_id: recepcionId,
                ordenes: ordenes
            },
            beforeSend: function() {
                btn.prop('disabled', true).html('<i class="fas fa-clock me-2"></i> Revisando...');
            },
            success: function(response) {
                toastr.success(response.message);
                $('.main-kit-collapse').find('.accordion.accordion-flush button').each(function() {
                    const $iconContainer = $(this).find('span.px-1');
                    if ($iconContainer.length) {
                        $iconContainer.empty().html('<i class="fas fa-check-double text-success" title="Orden revisada"></i>');
                    }
                });
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Revisado');
                if (typeof cargarTareas === 'function') {
                    cargarTareas(recepcionId, atencionId);
                }
                setTimeout(function() {
                    window.location.href = "{{ route('recepcion.solicitudes') }}";
                }, 1500);
            },
            error: function(xhr) {
                console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en revisar-orden click:", xhr);
                btn.prop('disabled', false).html('<i class="fas fa-clipboard-check"></i> Revisar');
                toastr.error(xhr.responseJSON?.message || 'Error al revisar');
            }
        });
    });
    $(document).on('click', '#corregir-orden', function() { // Corregir Orden (Receptor)
        const btn = $(this);
        const atencionId = btn.data('atencion-id');
        const recepcionId = $('#revisar-orden').data('recepcion-id'); // Obteniendo recepcionId del otro botón
        let ordenes = [];
        $('.main-kit-collapse').each(function() {
            const collapseDiv = $(this);
            const ordenId = collapseDiv.attr('id').replace('collapse', '');
            const accordionButton = collapseDiv.prev().find('.accordion-button');
            const kitText = accordionButton.text().trim();
            const kitId = kitText.split(' -')[0].trim();
            const unidadesInput = $(`#unidades_${ordenId}`);
            const unidades = unidadesInput.val();
            if (!unidades || !ordenId || !kitId) return;
            let detalles = [];
            collapseDiv.find('.accordion.accordion-flush').each(function() {
                const detalleAccordion = $(this);
                const accordionId = detalleAccordion.attr('id');
                const productIdInput = detalleAccordion.find(`input[id^="productId_"]`);
                const productoIdOriginal = productIdInput.data('original-id');
                const primerRadio = detalleAccordion.find('input[type="radio"]').first();
                const radioName = primerRadio.attr('name');
                if (!radioName) return;
                const radioSeleccionado = detalleAccordion.find(`input[name="${radioName}"]:checked`);
                const productoIdNuevo = radioSeleccionado.length > 0 ? radioSeleccionado.val() : productoIdOriginal;
                if (productoIdOriginal) {
                    detalles.push({
                        kit_id: parseInt(kitId),
                        producto_id_original: productoIdOriginal,
                        producto_id_nuevo: productoIdNuevo
                    });
                }
            });
            if (detalles.length > 0) {
                ordenes.push({
                    orden_id: parseInt(ordenId),
                    unidades: parseInt(unidades),
                    detalles: detalles
                });
            }
        });
        if (ordenes.length === 0) {
            toastr.error('No se encontraron órdenes para corregir.');
            return;
        }
        $.ajax({
            url: '{{ route('recepcion.corregir-orden') }}',
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}', 
                atencion_id: atencionId,
                recepcion_id: recepcionId, // Enviando el id de recepción
                ordenes: ordenes
            },
            beforeSend: function() {
                btn.prop('disabled', true).html('<i class="fas fa-clock me-2"></i> Procesando...');
            },
            success: function(response) {
                toastr.success(response.message || 'Orden corregida exitosamente');
                if (response.productos_cambiados && response.productos_cambiados.length > 0) {
                    response.productos_cambiados.forEach(item => {
                        const $btn = $(`button[data-orden-id="${item.orden_id}"][data-kit-id="${item.kit_id}"]`).filter(function() {
                            return $(this).find('input[id^="productId_"]').val() == item.producto_id;
                        });
                        if ($btn.length) {
                            const $icon = $btn.find('span.px-1 i.fas');
                            if ($icon.length) {
                                $icon.attr('class', 'fas fa-clock text-muted')
                                     .attr('title', 'Pendiente de revisión');
                                console.log(`Icono actualizado a reloj para producto ${item.producto_id} en orden ${item.orden_id}`);
                            }
                        }
                    });
                }
                setTimeout(function() {
                    window.location.href = "{{ route('recepcion.solicitudes') }}";
                }, 1500);
                btn.prop('disabled', false).html('<i class="fas fa-pencil-alt"></i> Corregir');
            },
            error: function(xhr) {
                console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en corregir-orden click:", xhr);
                btn.prop('disabled', false).html('<i class="fas fa-pencil-alt"></i> Corregir');
                toastr.error(xhr.responseJSON?.message || 'Error al corregir la orden');
            }
        });
    });

    // --- ACTUALIZACIÓN EN TIEMPO REAL (VÍA NOTIFICACIONES CON CARGA ÚTIL) ---
    function procesarActualizacionStock(detalles) {
        if (!detalles || !Array.isArray(detalles)) return;
        
        detalles.forEach(item => {
            const $btn = $(`button[data-orden-id="${item.orden_id}"][data-kit-id="${item.kit_id}"]`).filter(function() {
                return $(this).find('input[id^="productId_"]').val() == item.producto_id;
            });

            if ($btn.length) {
                const $icon = $btn.find('span.px-1 i.fas');
                if ($icon.length) {
                    let newClass = 'fa-clock text-muted';
                    let title = 'Pendiente de revisión';

                    if (item.stock_existencias == 1 || item.stock_fisico_existencias == 1) {
                        newClass = 'fa-check text-success';
                        title = 'Existencias verificadas';
                    } else if (item.stock_existencias == 0 || item.stock_fisico_existencias == 0) {
                        newClass = 'fa-times text-danger';
                        title = 'Sin existencias';
                    }

                    if (!$icon.hasClass(newClass.split(' ')[0]) || !$icon.hasClass(newClass.split(' ')[1])) {
                        $icon.attr('class', 'fas ' + newClass).attr('title', title);
                        console.log(`Icono actualizado vía notificación: Orden ${item.orden_id}, Producto ${item.producto_id}`);
                    }
                }
            }
        });
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('notification-received', (data) => {
            if (data.payload && data.payload.detalles) {
                console.log('Recibida carga útil de stock, actualizando vista...');
                procesarActualizacionStock(data.payload.detalles);
            }
        });
    });
</script>


@endpush
