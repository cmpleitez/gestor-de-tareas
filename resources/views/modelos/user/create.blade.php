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
                                <p>NUEVO USUARIO </p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('user') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('user.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- NOMBRE --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <div class="controls">
                                            <input type="text" name="name"
                                                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" required
                                                data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$"
                                                data-validation-containsnumber-message="El nombre debe contener solo letras (incluyendo tildes), no se permiten dobles espacios entre palabras, ni espacios al principio o final de las palabras."
                                                placeholder="Nombre del nuevo usuario" value="{{ old('name') }}">
                                            @error('name')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('name') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                {{-- OFICINA --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="oficina_id">Oficina</label>
                                        <select class="select2 form-control" id="oficina_id" name="oficina_id" required>
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
                                {{-- CORREO --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Correo electrónico</label>
                                        <div class="controls">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="correo@ejemplo.com"
                                                data-validation-required-message="El correo electrónico es requerido"
                                                value="{{ old('email') }}">
                                        </div>
                                        @error('email')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('email') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- CLAVE --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Clave</label>
                                        <div class="controls">
                                            <input type="password" name="password" class="form-control"
                                                data-validation-required-message="La clave debe ser una cadena de 6 a 16 caracteres"
                                                minlength="6" maxlength="16"
                                                placeholder="Una contraseña de 6 a 16 caracteres"
                                                value="{{ old('password') }}">
                                        </div>
                                        @error('password')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('password') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- CONFIRMAR CLAVE --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Confirmar clave</label>
                                        <div class="controls">
                                            <input type="password" name="password_confirmation" class="form-control"
                                                data-validation-match-match="password"
                                                data-validation-required-message="Se requiere una clave de confirmación"
                                                minlength="6" maxlength="16" placeholder="Clave de confirmación"
                                                value="{{ old('password_confirmation') }}">
                                        </div>
                                        @error('password_confirmation')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('password_confirmation') }}
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
@stop
