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
                                <p>NUEVO EQUIPO </p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('equipo') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('equipo.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- OFICINA --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Oficina</label>
                                        <div class="controls">
                                            <select name="oficina_id"
                                                class="form-control {{ $errors->has('oficina_id') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="La oficina es requerida" required>
                                                <option value="">Seleccione una oficina</option>
                                                @foreach ($oficinas as $oficina)
                                                    <option value="{{ $oficina->id }}"
                                                        {{ old('oficina_id') == $oficina->id ? 'selected' : '' }}>
                                                        {{ $oficina->oficina }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('oficina_id')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('oficina_id') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- NOMBRE --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Equipo</label>
                                        <div class="controls">
                                            <input type="text" name="equipo" id="equipo"
                                                class="form-control {{ $errors->has('equipo') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="Este campo es obligatorio"
                                                data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$"
                                                data-validation-containsnumber-message="Solo se permiten letras, sin espacios al inicio/final ni dobles espacios"
                                                data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                                data-clear="true" minlength="3" placeholder="Nombre del nuevo equipo"
                                                value="{{ old('equipo') }}" required>
                                            @error('equipo')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('equipo') }}
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
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
