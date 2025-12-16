@extends('servicios')

@push('css')
<style>
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
</style>
@endpush

@section('content')
<div class="container">
    @if($atencion->isEmpty())
    <div class="alert alert-info">
        Aún no ha agregado artículos al carrito
    </div>
    @else
    <!--ENCABEZADO DE LA ORDEN-->
    <div class="row">
        <div class="col">
            <h1>NUMERO DE ATENCIÓN {{ $atencion_id_ripped }}</h1>
        </div>
        <div class="col text-right">
            <h1>TOTAL ORDEN: ...</h1>
        </div>
    </div>
    <!--ORDEN DE COMPRAS-->
    <div class="row">
        <div class="col-12 col-md-9">
            <h6>NOMBRE DEL KIT</h6>
        </div>
        <div class="col-12 col-md-1 text-center">
            <h6>UNIDADES</h6>
        </div>
        <div class="col-12 col-md-1 text-center">
            <h6>PRECIO</h6>
        </div>
        <div class="col-12 col-md-1 text-center">
            <h5>ACCIONES</h5>
        </div>
    </div>
    <div class="row"> <!--Kits-->
        @php
        $currentAtencion = $atencion->first();
        @endphp
        @if($currentAtencion && $currentAtencion->ordenes)
            @foreach($currentAtencion->ordenes as $orden)

                {{--nuevo acordion--}}
                <div class="row d-flex align-items-center">
                    <div class="col-12 col-md-9">
                        <div class="accordion" id="accordion{{ $orden->id }}">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $orden->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $orden->id }}" aria-expanded="false" aria-controls="collapse{{ $orden->id }}">
                                        {{ $orden->kit_id }} - {{ $orden->kit->kit }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $orden->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $orden->id }}" data-bs-parent="#accordion{{ $orden->id }}">
                                <div class="accordion-body">
                                    
                                    @foreach($orden->detalle as $index => $detalle)
                                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Accordion Item #1
                                                </button>
                                                </h2>
                                                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first item's accordion body.</div>
                                                </div>
                                            </div>
                                        </div>                             
                                    @endforeach

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-1 text-center">
                        {{ $orden->unidades }}
                    </div>
                    <div class="col-12 col-md-1 text-center">
                        {{ $orden->precio }}
                    </div>
                    <div class="col-12 col-md-1 text-center">
                        <a href="#" role="button"
                            data-html="true"
                            data-placement="bottom"
                            class="button_delete align-center border border-danger-dark text-danger-dark btn-danger-light">
                            <i class="fas fa-trash"></i>
                        </a>                
                    </div>
                </div>


            {{--viejo acordion--}}
            @php
            $headingId = 'heading' . $orden->id;
            $accordionId = 'accordion' . $orden->id;
            @endphp
            <div class="col-12 col-md-9"> <!--Nombre del Kit-->
                <div class="accordion collapse-icon accordion-icon-rotate">
                    <div class="collapse-header" style="background-color:rgb(255, 255, 255) !important; min-height: 2em;">
                        <div id="{{ $headingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $accordionId }}" aria-expanded="false" aria-controls="{{ $accordionId }}" role="tablist">
                            {{ $orden->kit->kit .'Kit ID: ' . $orden->kit_id }}
                        </div>
                        <div id="{{ $accordionId }}" role="tabpanel" aria-labelledby="{{ $headingId }}" class="collapse">
                            <div class="card-content">
                                <!--Productos-->
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach($orden->detalle as $index => $detalle)
                                            @php
                                            $detHeadingId = 'heading_det_' . $orden->id . '_' . $index;
                                            $detAccordionId = 'accordion_det_' . $orden->id . '_' . $index;
                                            @endphp
                                            <div class="accordion collapse-icon accordion-icon-rotate mb-2">
                                                <div class="collapse-header" style="background-color:rgb(255, 255, 255) !important; min-height: 2em;">
                                                    <div id="{{ $detHeadingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $detAccordionId }}" aria-expanded="false" aria-controls="{{ $detAccordionId }}" role="tablist">
                                                        <span id="productName_{{ $detAccordionId }}">{{ $detalle->producto->id }} - {{ $detalle->producto->producto }}</span>
                                                        <span class="badge badge-primary badge-pill ml-auto">{{ $detalle->unidades }}</span>
                                                    </div>
                                                    <div id="{{ $detAccordionId }}" role="tabpanel" aria-labelledby="{{ $detHeadingId }}" class="collapse">
                                                        <div class="card-content">
                                                            <!--Equivalentes-->
                                                            <div class="card-body">
                                                                @php
                                                                $kitProducto = $detalle->producto->kitProductos->where('kit_id', $orden->kit_id)->first();
                                                                @endphp
                                                                @if($kitProducto)
                                                                <div class="d-flex flex-wrap">
                                                                    <label class="card rounded border mb-2 shadow-none" {{-- Equivalente predeterminado --}} style="width: 150px; margin-right: 10px; cursor: pointer;">
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
                                                                    @foreach($kitProducto->equivalentes as $equivalente) {{-- Equivalentes --}}
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
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                


            </div>
            <div class="col-12 col-md-1"> <!--Unidades-->
                {{ $orden->unidades }}
            </div>
            <div class="col-12 col-md-1"> <!--Precio-->
                {{ number_format($orden->precio, 2) }}
            </div>
            <div class="col-12 col-md-1"> <!--Acciones-->
                <a href="#" role="button"
                    data-html="true"
                    data-placement="bottom"
                    class="button_delete align-center border border-danger-dark text-danger-dark btn-danger-light">
                    <i class="bx bxs-eraser"></i>
                </a>
            </div>



            @endforeach
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function updateProductName(radio) {
        const targetSelector = radio.getAttribute('data-name-target');
        const productName = radio.getAttribute('data-product-name');
        const targetElement = document.querySelector(targetSelector);

        if (targetElement && productName) {
            targetElement.textContent = productName;
        }
    }

</script>
@endpush
