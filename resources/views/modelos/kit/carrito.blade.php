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
        No hay atenciones pendientes en el carrito.
    </div>
    @else
    <div class="row">
        <div class="col">
            <h1>NUMERO DE ATENCIÓN {{ $atencion->first()->id }}</h1>
        </div>
    </div>
    <div class="row">
        @php
            $currentAtencion = $atencion->first();
            $groupedDetails = $currentAtencion ? $currentAtencion->atencionDetalles->groupBy('kit_id') : collect();
        @endphp

        @foreach($groupedDetails as $kitId => $detalles)
        @php
            $kit = $detalles->first()->kit;
            $headingId = 'heading' . $kitId;
            $accordionId = 'accordion' . $kitId;
        @endphp
        <div class="col-12 col-md-10"> <!--Kit-->
            <div class="accordion collapse-icon accordion-icon-rotate">
                <div class="collapse-header" style="background-color:rgb(255, 255, 255) !important; min-height: 2em;">
                    <div id="{{ $headingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $accordionId }}" aria-expanded="false" aria-controls="{{ $accordionId }}" role="tablist">
                        {{ $kit->kit }}
                    </div>
                    <div id="{{ $accordionId }}" role="tabpanel" aria-labelledby="{{ $headingId }}" class="collapse">
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($detalles as $detalle)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $detalle->producto->producto }}
                                        <span class="badge badge-primary badge-pill">{{ $detalle->unidades }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-1"> <!--Precio-->
            {{ $detalles->first()->precio }}
        </div>
        <div class="col-12 col-md-1"> <!--Acciones-->
            <button type="button" class="btn btn-primary">Retirar</button>
        </div>
        @endforeach
    </div>
    @endif
</div>
@stop
