@extends('dashboard')

@php
    // Paletas por nivel/estado
    $nivel = [
        'ok'     => ['valor' => 'text-success', 'chip' => 'bg-rgba-success', 'punto' => 'bg-success', 'icono' => 'bx-check'],
        'warn'   => ['valor' => 'text-warning', 'chip' => 'bg-rgba-warning', 'punto' => 'bg-warning', 'icono' => 'bx-error'],
        'danger' => ['valor' => 'text-danger',  'chip' => 'bg-rgba-danger',  'punto' => 'bg-danger',  'icono' => 'bx-x'],
    ];

    // Categorías del reporte, de mayor a menor criticidad.
    $categorias = [
        ['clave' => 'danger', 'titulo' => 'Graves'],
        ['clave' => 'warn',   'titulo' => 'Importantes'],
        ['clave' => 'ok',     'titulo' => 'Protegidos'],
    ];

    // Unifica KPIs (por `nivel`) y postura (por `estado`) en una sola forma de fila.
    $items = array_merge(
        array_map(fn ($k) => [
            'clave' => $k['clave'], 'etiqueta' => $k['etiqueta'], 'nivel' => $k['nivel'],
            'valor' => $k['valor'], 'meta' => null, 'nota' => $k['detalle'],
        ], $security['kpis'] ?? []),
        array_map(fn ($p) => [
            'clave' => $p['clave'], 'etiqueta' => $p['etiqueta'], 'nivel' => $p['estado'],
            'valor' => $p['actual'], 'meta' => "esperado: {$p['esperado']}", 'nota' => $p['nota'],
        ], $security['posture'] ?? [])
    );

    // Filas agrupadas por categoría (para las secciones).
    $grupos = array_map(fn ($c) => $c + ['items' => array_values(array_filter($items, fn ($i) => $i['nivel'] === $c['clave']))], $categorias);

    // Indicadores resumen (derivados de los datos, sin valores fijos).
    $entorno = collect($security['posture'] ?? [])->firstWhere('clave', 'app_env')['actual'] ?? '—';
    $graves = count(array_filter($items, fn ($i) => $i['nivel'] === 'danger'));
    $importantes = count(array_filter($items, fn ($i) => $i['nivel'] === 'warn'));
    $protegidos = count(array_filter($items, fn ($i) => $i['nivel'] === 'ok'));
    $total = count($items);
@endphp

@section('css')
<style>
    .indicador-icono {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .indicador-punto {
        width: 0.625rem;
        height: 0.625rem;
        border-radius: 50%;
        display: inline-block;
    }
    .lista-indicadores .list-group-item {
        border-left: 0;
        border-right: 0;
    }
</style>
@endsection

@section('contenedor')
<div class="row">
    <div class="col-12">
        <h4 class="mb-2">Indicadores de seguridad</h4>
    </div>
</div>

{{-- Tarjetas-indicadores: una por categoría (+ entorno) --}}
<div class="row">
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted text-uppercase font-small-2 mb-0">Entorno</p>
                <h3 class="mb-0">{{ $entorno }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted text-uppercase font-small-2 mb-0">Graves</p>
                <h3 class="mb-0 {{ $graves ? $nivel['danger']['valor'] : $nivel['ok']['valor'] }}">{{ $graves }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted text-uppercase font-small-2 mb-0">Importantes</p>
                <h3 class="mb-0 {{ $importantes ? $nivel['warn']['valor'] : $nivel['ok']['valor'] }}">{{ $importantes }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted text-uppercase font-small-2 mb-0">Protegidos</p>
                <h3 class="mb-0 {{ $nivel['ok']['valor'] }}">{{ $protegidos }}/{{ $total }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- Secciones por categoría --}}
<div class="row">
    <div class="col-12">
        @foreach($grupos as $grupo)
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="indicador-punto mr-1 {{ $nivel[$grupo['clave']]['punto'] }}"></span>
                    <div class="card-title mb-0">{{ $grupo['titulo'] }}</div>
                    <span class="badge badge-light-secondary ml-1">{{ count($grupo['items']) }}</span>
                </div>
                <div class="card-content">
                    @if(!count($grupo['items']))
                        <div class="card-body pt-0">
                            <p class="text-muted mb-0">Sin elementos en esta categoría.</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush lista-indicadores">
                            @foreach($grupo['items'] as $item)
                                <li class="list-group-item d-flex align-items-start">
                                    <span class="indicador-icono mr-1 {{ $nivel[$item['nivel']]['chip'] }} {{ $nivel[$item['nivel']]['valor'] }}">
                                        <i class="bx {{ $nivel[$item['nivel']]['icono'] }}"></i>
                                    </span>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <span class="font-weight-bold">{{ $item['etiqueta'] }}</span>
                                            <span class="font-small-2 text-muted">
                                                <span class="font-weight-bold {{ $nivel[$item['nivel']]['valor'] }}">{{ $item['valor'] }}</span>
                                                @if($item['meta'])
                                                    <span> · {{ $item['meta'] }}</span>
                                                @endif
                                            </span>
                                        </div>
                                        @if($item['nota'])
                                            <p class="font-small-2 text-muted mb-0 mt-25">{{ $item['nota'] }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
