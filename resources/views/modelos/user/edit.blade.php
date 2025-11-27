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
                                <p>USUARIO {{ $user->name }}</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('user') !!}">
                                    <div class="badge badge-pill btn-warning">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('user.update', $user->id) }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                {{-- IMAGEN --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Fotografia del Operador <small class="text-muted">(Máximo 5 MB, solo
                                                JPEG/PNG)</small></label>
                                        <div class="controls">
                                            <input type="file" name="image_path" class="form-control"
                                                accept="image/jpeg,image/jpg,image/png"
                                                onchange="validateFileSize(this, 5)">
                                        </div>
                                        <small class="form-text text-muted">Formatos permitidos: JPEG, JPG, PNG. Tamaño
                                            máximo: 5 MB</small>
                                        @error('image_path')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('image_path') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- OFICINA --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="oficina_id">Oficina</label>
                                        <div class="controls">
                                            <select
                                                class="select2 form-control {{ $errors->has('oficina_id') ? 'is-invalid' : '' }}"
                                                id="oficina_id" name="oficina_id"
                                                data-validation-required-message="Este campo es obligatorio" required>
                                                <option value="">Seleccione una oficina</option>
                                                @foreach ($oficinas as $oficina)
                                                    <option value="{{ $oficina->id }}"
                                                        {{ old('oficina_id', $user->oficina_id) == $oficina->id ? 'selected' : '' }}>
                                                        {{ $oficina->oficina }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
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
                                        <label>Correo</label>
                                        <div class="controls">
                                            <input type="email" name="email" id="email"
                                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="El email es requerido."
                                                data-validation-email-message="Debe ser un correo electrónico válido"
                                                data-clear="true" placeholder="correo@ejemplo.com"
                                                value="{{ old('email', $user->email) }}" required>
                                        </div>
                                        @error('email')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('email') }}
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
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            window.validateFileSize = function(input, maxSizeMB) {
                const file = input.files[0];
                if (file) {
                    const fileSizeMB = file.size / (1024 * 1024);
                    if (fileSizeMB > maxSizeMB) {
                        alert('El archivo es demasiado grande. El tamaño máximo permitido es ' + maxSizeMB +
                            'MB.');
                        input.value = '';
                        return false;
                    }
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Solo se permiten archivos JPEG, JPG o PNG.');
                        input.value = '';
                        return false;
                    }
                }
                return true;
            };
        });
    </script>
@stop
