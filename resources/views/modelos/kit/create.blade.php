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
                    <p>NUEVO KIT</p>
                </div>
                <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                    <a href="{!! route('kit') !!}">
                        <div class="badge badge-pill badge-primary">
                            <i class="bx bx-arrow-back font-medium-3"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- FORMULARIO -->
    <form class="form-horizontal" action="{{ route('kit.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group"> {{-- Nombre del kit --}}
                            <label for="kit">Nombre del kit</label>
                            <input type="text" name="kit" id="kit"
                                class="form-control {{ $errors->has('kit') ? 'is-invalid' : '' }}"
                                data-validation-required-message="Este campo es obligatorio"
                                data-validation-containsnumber-regex="^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$"
                                data-validation-containsnumber-message="Solo se permiten letras, números, paréntesis y guion medio, sin espacios al inicio/final ni dobles espacios"
                                data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                data-clear="true" minlength="3" placeholder="Nombre para el nuevo kit"
                                value="{{ old('kit') }}" required>
                            <div class="help-block"></div>
                            @error('kit')
                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                {{ $errors->first('kit') }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group"> {{-- Ruta imagen --}}
                            <label>Fotografia del Kit <small class="text-muted">(Máximo 10 MB, solo
                                    JPEG/PNG)</small></label>
                            <input type="file" name="image_path" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/jpg,image/png" onchange="validateFileSize(this, 10)">
                            <small class="form-text text-muted">Formatos permitidos: JPEG, JPG, PNG. Tamaño
                                máximo: 10 MB</small>
                        </div>
                        @error('image_path')
                        <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                            {{ $errors->first('image_path') }}
                        </div>
                        @enderror

                        <div class="form-group"> {{-- Precio --}}
                            <label for="precio">Precio</label>
                            <input type="text" name="precio" id="precio" class="form-control input-currency {{ $errors->has('precio') ? 'is-invalid' : '' }}" value="{{ old('precio') }}" data-clear="true" required>
                            <div class="help-block"></div>
                            @error('precio')
                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                {{ $errors->first('precio') }}
                            </div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- GUARDAR -->
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
@stop

@section('js')
@stop
