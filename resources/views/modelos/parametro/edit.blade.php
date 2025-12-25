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
                                <p>EDITAR PARAMETRO</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('recepcion.parametros') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('recepcion.parametros-update', $parametro->id) }}"
                    method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- NOMBRE --}}
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Parámetro --}}
                                        <label for="parametro">Parámetro</label>
                                        <input type="text" name="parametro" id="parametro"
                                            class="form-control {{ $errors->has('parametro') ? 'is-invalid' : '' }}"
                                            data-validation-required-message="El nombre del parámetro es requerido"
                                            data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$"
                                            data-validation-containsnumber-message="El nombre debe contener solo letras y paréntesis (incluyendo tildes), no se permiten dobles espacios entre palabras, ni espacios al principio o final de las palabras."
                                            placeholder="Nombre del nuevo parámetro"
                                            value="{{ old('parametro', $parametro->parametro) }}" required>
                                        <div class="help-block"></div>
                                        @error('parametro')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('parametro') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- VALOR --}}
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Valor --}}
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
                                {{-- UNIDAD DE MEDIDA --}}
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Unidad de medida --}}
                                        <label for="unidad_medida">Unidad de medida</label>
                                        <select name="unidad_medida" id="unidad_medida" class="form-control" required>
                                            <option value="">Seleccione o agregue una unidad</option>
                                            <option value="minutos"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'minutos' ? 'selected' : '' }}>
                                                minutos</option>
                                            <option value="horas"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'horas' ? 'selected' : '' }}>
                                                horas</option>
                                            <option value="días"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'días' ? 'selected' : '' }}>
                                                días</option>
                                            <option value="semanas"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'semanas' ? 'selected' : '' }}>
                                                semanas</option>
                                            <option value="meses"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'meses' ? 'selected' : '' }}>
                                                meses</option>
                                            <option value="años"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'años' ? 'selected' : '' }}>
                                                años</option>
                                            <option value="unidades"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'unidades' ? 'selected' : '' }}>
                                                unidades</option>
                                            <option value="unidades em"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'unidades em' ? 'selected' : '' }}>
                                                unidades em</option>
                                            <option value="unidades rem"
                                                {{ old('unidad_medida', $parametro->unidad_medida) == 'unidades rem' ? 'selected' : '' }}>
                                                unidades rem</option>
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
