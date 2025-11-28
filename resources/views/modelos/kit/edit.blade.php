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
                            <p>EDITAR KIT</p>
                        </div>
                        <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                            <a href="{!! route('kit') !!}">
                                <div class="badge badge-pill btn-warning">
                                    <i class="bx bx-arrow-back font-medium-3"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FORMULARIO -->
            <form class="form-horizontal" action="{{ route('kit.update', $kit->id) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group"> {{-- Kit --}}
                                    <label for="kit">Kit</label>
                                    <input type="text" name="kit" id="kit" class="form-control {{ $errors->has('kit') ? 'is-invalid' : '' }}" data-validation-required-message="Este campo es obligatorio" data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$" data-validation-containsnumber-message="Solo se permiten letras y paréntesis, sin espacios al inicio/final ni dobles espacios" data-validation-minlength-message="El nombre debe tener al menos 3 caracteres" data-clear="true" minlength="3" placeholder="Nombre del kit" value="{{ old('kit', $kit->kit) }}" required>
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
                                    @error('image_path')
                                    <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                        {{ $errors->first('image_path') }}
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            @foreach ($kit->productos as $producto)
                                <div class="col-md-12">
                                    <div class="form-group"> {{-- Unidades --}}
                                        <label for="{{ $producto->id }}">{{ $producto->producto }}</label>
                                        <input type="number" name="producto[{{ $producto->id }}][unidades]" id="producto_{{ $producto->id }}_unidades" class="form-control" value="{{ old('producto.' . $producto->id . '.unidades', $producto->pivot->unidades) }}" required>
                                    </div>
                                    @error('producto[' . $producto->id . '][unidades]')
                                    <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                        {{ $errors->first('producto[' . $producto->id . '][unidades]') }}
                                    </div>
                                    @enderror
                                </div>
                            @endforeach
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
