@extends('dashboard')

@section('css')
@stop

@section('contenedor')
<div class="card">
    <!-- CABECERA -->
    <div class="card-header pt-1">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between" style="padding: 0rem;">
                <div class="col-md-10 align-items-center" style="padding-left: 0.5rem;">
                    <p>{{ strtoupper($parametro->parametro) }}</p>
                </div>
                <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                    <a href="{!! route('parametro') !!}">
                        <div class="badge badge-pill badge-warning">
                            <i class="bx bx-arrow-back font-medium-3"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- FORMULARIO -->
    <form class="form-horizontal" action="{{ route('parametro.update', $parametro->id) }}"
        method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12"> {{-- Valor --}}
                        <div class="form-group"> 
                            <label for="valor">Valor</label>
                            <input type="text" name="valor" id="valor" class="form-control"
                                value="{{ old('valor', $parametro->valor) }}" required>
                            <div class="help-block"></div>
                            @error('valor')
                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                    {{ $errors->first('valor') }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12"> {{-- Unidad de medida --}}
                        <div class="form-group">
                            <label for="unidad_medida">Unidad de medida</label>
                            <select name="unidad_medida" id="unidad_medida" class="form-control" required>
                                <option value="">Seleccione o agregue una unidad</option>
                                @php
                                    $unidades_predefinidas = ['minutos', 'horas', 'días', 'semanas', 'meses', 'años', 'unidades', 'unidades em', 'unidades rem', 'segundos', 'boolean'];
                                    $unidad_actual = old('unidad_medida', $parametro->unidad_medida);
                                    $es_personalizada = !in_array($unidad_actual, $unidades_predefinidas) && !empty($unidad_actual);
                                @endphp
                                @foreach($unidades_predefinidas as $u)
                                    <option value="{{ $u }}" {{ $unidad_actual == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                                @if($es_personalizada)
                                    <option value="{{ $unidad_actual }}" selected>{{ $unidad_actual }}</option>
                                @endif
                            </select>
                            <div class="help-block"></div>
                            @error('unidad_medida')
                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                    {{ $errors->first('unidad_medida') }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- GUARDAR --}}
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-warning">Guardar</button>
        </div>
    </form>
</div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar Select2 con opción de agregar nuevas opciones
            $('#unidad_medida').select2({
                tags: true, // Permite agregar nuevas opciones
                placeholder: "Seleccione o agregue una unidad",
                allowClear: true, // Permite limpiar la selección
                width: '100%'
            });
        });
    </script>
@stop
