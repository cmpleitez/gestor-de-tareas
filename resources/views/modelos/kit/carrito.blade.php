@extends('dashboard')

@section('css')
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
@stop

@section('contenedor')

<div class="container"> 
    @if($atencion->isEmpty())
        <div class="alert alert-info">
            Aún no ha agregado artículos al carrito
        </div>
    @else
        <!--TITULO-->
        <div class="row">
            <div class="col">
                <h1>NUMERO DE ATENCIÓN {{ $atencion->first()->id }}</h1>
            </div>
        </div>

        <!--GRID DE DATOS-->
        <div class="row">
            <div class="col-12 col-md-10">
                <h5>NOMBRE DEL KIT</h5>
            </div>
            <div class="col-12 col-md-1">
                <h5>PRECIO</h5>
            </div>
            <div class="col-12 col-md-1">
                <h5>ACCIONES</h5>
            </div>
        </div>
        <div class="row">
            @php
                $currentAtencion = $atencion->first();
                $groupedDetails = $currentAtencion ? $currentAtencion->atencionDetalles->groupBy('kit_id') : collect();
            @endphp
            @foreach($groupedDetails as $kitId => $productos)
                @php
                    $kit = $productos->first()->kit;
                    $headingId = 'heading' . $kitId;
                    $accordionId = 'accordion' . $kitId;
                @endphp
                <div class="col-12 col-md-10"> 
                    <div class="accordion collapse-icon accordion-icon-rotate"> <!--Kits-->
                        <div class="collapse-header" style="background-color:rgb(255, 255, 255) !important; min-height: 2em;">
                            <div id="{{ $headingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $accordionId }}" aria-expanded="false" aria-controls="{{ $accordionId }}" role="tablist">
                                {{ $kit->kit }}
                            </div>
                            <div id="{{ $accordionId }}" role="tabpanel" aria-labelledby="{{ $headingId }}" class="collapse">
                                <div class="card-content"> <!--Productos-->
                                    <div class="card-body">
                                        <div class="list-group">
                                            @foreach($productos as $index => $item)
                                                @php
                                                    $detHeadingId = 'heading_det_' . $kitId . '_' . $index;
                                                    $detAccordionId = 'accordion_det_' . $kitId . '_' . $index;
                                                @endphp
                                                <div class="accordion collapse-icon accordion-icon-rotate mb-2"> 
                                                    <div class="collapse-header" style="background-color:rgb(255, 255, 255) !important; min-height: 2em;">
                                                        <div id="{{ $detHeadingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $detAccordionId }}" aria-expanded="false" aria-controls="{{ $detAccordionId }}" role="tablist">
                                                            Unidades
                                                            <span class="badge badge-primary badge-pill ml-auto">{{ $item->unidades }}</span>
                                                        </div>
                                                        <div id="{{ $detAccordionId }}" role="tabpanel" aria-labelledby="{{ $detHeadingId }}" class="collapse">
                                                            <div class="card-content"> <!--Equivalentes-->
                                                                <div class="card-body">
                                                                    @php
                                                                        $kitProducto = $item->producto->kitProductos->where('kit_id', $kitId)->first();
                                                                    @endphp
                                                                    @if($kitProducto)
                                                                        <div class="d-flex flex-wrap">
                                                                            <label class="card rounded border mb-2 shadow-none" {{-- Equivalente predeterminado --}}
                                                                            style="width: 150px; margin-right: 10px; cursor: pointer;">
                                                                                <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                                    <div class="mb-2">
                                                                                        <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $kitProducto->producto->id }}" data-name-target="#heading_{{ $detAccordionId }}" data-product-name="{{ $kitProducto->producto->producto }}" {{ $item->producto_id == $kitProducto->producto->id ? 'checked' : '' }} onchange="updateProductName(this)">
                                                                                    </div>
                                                                                    <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                                        <span class="d-block">{{ $kitProducto->producto->producto }}</span>
                                                                                        <span class="badge badge-info badge-pill mt-1">Estándar</span>
                                                                                    </div>
                                                                                </div>
                                                                            </label>
                                                                            @foreach($kitProducto->equivalentes as $equivalente) {{-- Equivalentes --}}
                                                                                <label class="card rounded border mb-2 shadow-none" 
                                                                                style="width: 150px; margin-right: 10px; cursor: pointer;">
                                                                                    <div class="card-body p-2 d-flex flex-column align-items-center">
                                                                                        <div class="mb-2">
                                                                                            <input type="radio" name="radio_{{ $detAccordionId }}" value="{{ $equivalente->producto->id }}" data-name-target="#heading_{{ $detAccordionId }}" data-product-name="{{ $equivalente->producto->producto }}" {{ $item->producto_id == $equivalente->producto->id ? 'checked' : '' }} onchange="updateProductName(this)">
                                                                                        </div>
                                                                                        <div class="text-center d-flex flex-column justify-content-center flex-grow-1">
                                                                                            {{ $equivalente->producto->producto }}
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
                <div class="col-12 col-md-1">
                    <!--Precio-->
                    {{ $productos->first()->precio }}
                </div>
                <div class="col-12 col-md-1">
                    <!--Acciones-->
                    <button type="button" class="btn btn-primary">Retirar</button>
                </div>
            @endforeach
        </div>
    @endif
</div>
@stop
