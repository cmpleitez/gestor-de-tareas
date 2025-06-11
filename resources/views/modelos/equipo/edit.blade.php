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
                                <p>EDITAR EQUIPO </p>
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
                <form class="form-horizontal" action="{{ route('equipo.update', $equipo->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- NOMBRE --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Equipo</label>
                                        <div class="controls">
                                            <input type="text" name="equipo"
                                                class="form-control {{ $errors->has('equipo') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="El nombre del equipo es requerido"
                                                data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$"
                                                data-validation-containsnumber-message="El nombre debe contener solo letras (incluyendo tildes), no se permiten dobles espacios entre palabras, ni espacios al principio o final de las palabras."
                                                placeholder="Nombre del nuevo equipo"
                                                value="{{ old('equipo', $equipo->equipo) }}" required>
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
