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
                                <p>EDITAR SOLICITUD</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('solicitud') !!}">
                                    <div class="badge badge-pill btn-warning">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('solicitud.update', $solicitud->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- SOLICITUD --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Solicitud</label>
                                        <div class="controls">
                                            <input type="text" name="solicitud" id="solicitud"
                                                class="form-control {{ $errors->has('solicitud') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="Este campo es obligatorio"
                                                data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$"
                                                data-validation-containsnumber-message="Solo se permiten letras, sin espacios al inicio/final ni dobles espacios"
                                                data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                                data-clear="true" minlength="3" placeholder="Nombre de la nueva solicitud"
                                                value="{{ old('solicitud', $solicitud->solicitud) }}" required>
                                            @error('solicitud')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('solicitud') }}
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
@stop