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
                    <p>EDITAR PRODUCTO</p>
                </div>
                <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                    <a href="{!! route('producto') !!}">
                        <div class="badge badge-pill btn-warning">
                            <i class="bx bx-arrow-back font-medium-3"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- FORMULARIO -->
    <form class="form-horizontal" action="{{ route('producto.update', $producto->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group"> {{-- Producto --}}
                            <label for="producto">Producto</label>
                            <input type="text" name="producto" id="producto"
                                class="form-control {{ $errors->has('producto') ? 'is-invalid' : '' }}"
                                data-validation-required-message="Este campo es obligatorio"
                                data-validation-containsnumber-regex="^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$"
                                data-validation-containsnumber-message="Solo se permiten letras, números, paréntesis y guion medio, sin espacios al inicio/final ni dobles espacios"
                                data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                data-clear="true" minlength="3" placeholder="Nombre del producto"
                                value="{{ old('producto', $producto->producto) }}" required>
                            <div class="help-block"></div>
                            @error('producto')
                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                {{ $errors->first('producto') }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group"> {{-- Código --}}
                            <label for="codigo">Código</label>
                            <input type="text" name="codigo" id="codigo" 
                                class="form-control {{ $errors->has('codigo') ? 'is-invalid' : '' }}" 
                                style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase()"
                                data-validation-regex-regex="^[A-Z0-9ÁÉÍÓÚÑ]+( [A-Z0-9ÁÉÍÓÚÑ]+)*$" 
                                data-validation-regex-message="Solo se permiten letras mayúsculas y números, sin espacios al inicio/final ni dobles espacios" 
                                data-validation-minlength-message="El código debe tener al menos 3 caracteres" 
                                data-clear="true" minlength="3" placeholder="Código del producto" 
                                value="{{ old('codigo', $producto->codigo) }}">
                            <div class="help-block"></div>
                            @error('codigo')
                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                {{ $errors->first('codigo') }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group"> {{-- Modelo --}}
                            <label for="modelo_id">Modelo</label>
                            <select name="modelo_id" id="modelo_id" class="select2 form-control {{ $errors->has('modelo_id') ? 'is-invalid' : '' }}" data-placeholder="Seleccione un modelo" data-validation-required-message="Este campo es obligatorio" required>
                                <option value=""></option>
                                @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id }}" {{ old('modelo_id', $producto->modelo_id) == $modelo->id ? 'selected' : '' }}>
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
                        <div class="form-group"> {{-- Tipo --}}
                            <label for="tipo_id">Tipo</label>
                            <select name="tipo_id" id="tipo_id" class="form-control {{ $errors->has('tipo_id') ? 'is-invalid' : '' }}" data-validation-required-message="Este campo es obligatorio" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_id', $producto->tipo_id) == $tipo->id ? 'selected' : '' }}>
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
                            <input type="text" name="precio" id="precio" class="form-control input-currency {{ $errors->has('precio') ? 'is-invalid' : '' }}" value="{{ old('precio', $producto->precio) }}" data-clear="true" required>
                            <div class="help-block"></div>
                            @error('precio')
                            <div class="col-sm-6 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                {{ $errors->first('precio') }}
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
        $('#modelo_id').select2({
            placeholder: 'Seleccione un modelo'
            , allowClear: true
        });
    });
</script>
@stop
