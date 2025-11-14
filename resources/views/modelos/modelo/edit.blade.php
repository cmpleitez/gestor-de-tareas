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
                                <p>EDITAR MODELO</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('modelo') !!}">
                                    <div class="badge badge-pill btn-warning">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('modelo.update', $modelo->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Modelo</label>
                                        <div class="controls">
                                            <input type="text" name="modelo" id="modelo"
                                                class="form-control {{ $errors->has('modelo') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="Este campo es obligatorio"
                                                data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$"
                                                data-validation-containsnumber-message="Solo se permiten letras, sin espacios al inicio/final ni dobles espacios"
                                                data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                                data-clear="true" minlength="3" placeholder="Nombre del modelo"
                                                value="{{ old('modelo', $modelo->modelo) }}" required>
                                            @error('modelo')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('modelo') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Marca</label>
                                        <div class="controls">
                                            <select name="marca_id" id="marca_id"
                                                class="select2 form-control {{ $errors->has('marca_id') ? 'is-invalid' : '' }}"
                                                data-placeholder="Seleccione una marca"
                                                data-validation-required-message="Este campo es obligatorio" required>
                                                <option value=""></option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->id }}" {{ old('marca_id', $modelo->marca_id) == $marca->id ? 'selected' : '' }}>
                                                        {{ $marca->marca }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('marca_id')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('marca_id') }}
                                                </div>
                                            @enderror
                                        </div>
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
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#marca_id').select2({
            placeholder: 'Seleccione una marca',
            allowClear: true
        });
    });
</script>
@stop