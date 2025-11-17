@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <!-- CABECERA -->
                <div class="card-header pt-1">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0rem;">
                            <div class="col-md-10 align-items-center" style="padding-left: 0.5rem;">
                                <p>NUEVO PRODUCTO</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('producto') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('producto.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Producto --}}
                                        <label for="producto">Producto</label>
                                        <input type="text" name="producto" id="producto"
                                            class="form-control {{ $errors->has('producto') ? 'is-invalid' : '' }}"
                                            data-validation-required-message="Este campo es obligatorio"
                                            data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$"
                                            data-validation-containsnumber-message="Solo se permiten letras y paréntesis, sin espacios al inicio/final ni dobles espacios"
                                            data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                            data-clear="true" minlength="3" placeholder="Nombre del nuevo producto"
                                            value="{{ old('producto') }}" required>
                                        <div class="help-block"></div>
                                        @error('producto')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('producto') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Modelo --}}
                                        <label for="modelo_id">Modelo</label>
                                        <select name="modelo_id" id="modelo_id"
                                            class="select2 form-control {{ $errors->has('modelo_id') ? 'is-invalid' : '' }}"
                                            data-placeholder="Seleccione un modelo"
                                            data-validation-required-message="Este campo es obligatorio" required>
                                            <option value=""></option>
                                            @foreach($modelos as $modelo)
                                                <option value="{{ $modelo->id }}" {{ old('modelo_id') == $modelo->id ? 'selected' : '' }}>
                                                    {{ $modelo->modelo }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block"></div>
                                        @error('modelo_id')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('modelo_id') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Tipo --}}
                                        <label for="tipo_id">Tipo</label>
                                        <select name="tipo_id" id="tipo_id"
                                            class="select2 form-control {{ $errors->has('tipo_id') ? 'is-invalid' : '' }}"
                                            data-placeholder="Seleccione un tipo"
                                            data-validation-required-message="Este campo es obligatorio" required>
                                            <option value=""></option>
                                            @foreach($tipos as $tipo)
                                                <option value="{{ $tipo->id }}" {{ old('tipo_id') == $tipo->id ? 'selected' : '' }}>
                                                    {{ $tipo->tipo }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block"></div>
                                        @error('tipo_id')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('tipo_id') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Precio --}}
                                        <label for="precio">Precio</label>
                                        <input type="text" name="precio" id="precio" class="form-control input-currency {{ $errors->has('precio') ? 'is-invalid' : '' }}" value="{{ old('precio') }}" required>
                                        <div class="help-block"></div>
                                        @error('precio')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('precio') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Accesorio --}}
                                        <div class="checkbox checkbox-primary">
                                            <input type="hidden" name="accesorio" value="0">
                                            <input type="checkbox" name="accesorio" id="accesorio" value="1" {{ old('accesorio') == 1 ? 'checked' : '' }}>
                                            <label for="accesorio">Accesorio</label>
                                        </div>
                                        <div class="help-block"></div>
                                        @error('accesorio')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('accesorio') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- GUARDAR --}}
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#modelo_id').select2({
            placeholder: 'Seleccione un modelo',
            allowClear: true
        });
        $('#tipo_id').select2({
            placeholder: 'Seleccione un tipo',
            allowClear: true
        });
    });
</script>
@stop
