@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <!-- CABECERA -->
                <div class="card-header pt-1">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0rem;">
                            <div class="col-md-10 align-items-center" style="padding-left: 0.5rem;">
                                <p>USUARIO {{ $user->name }}</p>
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
                <form class="form-horizontal" action="{{ route('user.update', $user->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- NOMBRES Y APELLIDOS --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <input type="text" name="text" class="form-control" placeholder="Nombres y apellidos" 
                                            required  data-validation-required-message="El nombre completo del usuario es requerido"
                                            value="{{ old('name', $user->name) }}">
                                        </div>
                                    </div>
                                </div>
                                {{-- CORREO --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="controls">
                                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com"
                                            required data-validation-required-message="El correo electrónico es requerido"
                                            {{ old('email', $user->email) }}>
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

{{-- CAMPOS --}}
{{-- <div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <label for="name" class="form-label">NOMBRES Y APELLIDOS</label>
                    <input id="name" class="form-control" name="name" type="text" minlength="5"
                        maxlength="255" value="{{ old('name', $user->name) }}" required>
                    <div class="invalid-feedback">
                        Por favor, ingrese nombres y apellidos válidos (de 5 a 255 caracteres).
                    </div>
                    @error('name')
                        <div class="col-sm-12 badge bg-danger text-wrap">
                            {{ $errors->first('name') }}
                        </div>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3">
                    <label for="email" class="form-label">CORREO</label>
                    <input id="email" class="form-control" name="email" type="email" maxlength="255"
                        placeholder="email@ejemplo.com" value="{{ old('email', $user->email) }}"
                        required>
                    <div class="invalid-feedback">
                        Por favor, escriba un nombre de correo válido (correo@ejemplo.com).
                    </div>
                    @error('email')
                        <div class="col-sm-12 badge bg-danger text-wrap">
                            {{ $errors->first('email') }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 mb-3">
                    <label for="celular" class="form-label">CELULAR</label required>
                    <input class="form-control celular" name="celular" type="text"
                        placeholder="9999 9999" value="{{ old('celular', $user->celular) }}" required>
                    <div class="invalid-feedback">
                        Por favor, escriba un número de teléfono válido, formato ejemplo: 9999 9999.
                    </div>
                    @error('celular')
                        <div class="col-sm-12 badge bg-danger text-wrap">
                            {{ $errors->first('celular') }}
                        </div>
                    @enderror
                </div>
                <div class="col-sm-4 mb-3">
                    <label for="telefono" class="form-label">TELEFONO</label required>
                    <input class="form-control celular" name="telefono" type="text"
                        placeholder="9999 9999" value="{{ old('telefono', $user->telefono) }}">
                    <div class="invalid-feedback">
                        Por favor, escriba un número de teléfono válido, formato ejemplo: 9999 9999.
                    </div>
                    @error('telefono')
                        <div class="col-sm-12 badge bg-danger text-wrap">
                            {{ $errors->first('telefono') }}
                        </div>
                    @enderror
                </div>
                <div class="col-sm-4 mb-3">
                    <label for="oficina_id" class="form-label">Ubicación</label>
                    <select id="oficina_id" class="form-select" name="oficina_id">
                        <option disabled>Elija una oficina</option>
                        @foreach ($oficinas as $oficina)
                            <option value="{{ $oficina->id }}" {{ old('oficina_id', $user->oficina_id) == $oficina->id ? 'selected' : '' }}>
                                {{ $oficina->oficina }}</option>
                        @endforeach
                    </select>
                    @error('oficina_id')
                        <div class="col-sm-12 badge bg-danger text-wrap">
                            {{ $errors->first('oficina_id') }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div> --}}