@extends('dashboard')

@section('css')
@stop

@section('contenedor')
<div class="row justify-content-center">
    <div class="col-md-12">
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
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="form-group"> {{-- Datos de kit_producto --}}
                                            <label for="producto_{{ $producto->pivot->id }}_unidades">
                                                {{ $producto->id }} - {{ $producto->producto }}
                                            </label>
                                            <input type="number" name="producto[{{ $producto->pivot->id }}][unidades]" id="producto_{{ $producto->pivot->id }}_unidades" class="form-control" value="{{ old('producto.' . $producto->pivot->id . '.unidades', $producto->pivot->unidades) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group"> {{-- Abrir modal de producto equivalente --}}
                                            <button type="button" class="btn btn-outline-primary block" data-toggle="modal" data-target="#modal-nuevo-equivalente" data-kit-producto-id="{{ $producto->id }}" data-producto-id="{{ $producto->id }}" data-unidades="{{ $producto->pivot->unidades }}">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row"> {{-- Productos equivalentes --}}
                                    @foreach ($producto->equivalentes as $equivalente)
                                    <div class="col-md-3 col-sm-6 mb-sm-1">
                                        <div class="card">
                                            <div class="card-content">
                                                <img class="card-img-top img-fluid" src="{{ asset('app-assets/images/pages/operador.png') }}" alt="Producto alterno" />
                                                <div class="card-body">
                                                    <h4 class="card-title">{{ $equivalente->producto->producto }}</h4>
                                                    <p class="card-text">
                                                        This card has supporting text below as a natural lead-in to
                                                        additional content.
                                                    </p>
                                                    <small class="text-muted">Last updated 3 mins ago</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
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
            <!-- MODAL DE NUEVO PRODUCTO EQUIVALENTE -->
            <div class="modal fade text-left modal-borderless" id="modal-nuevo-equivalente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Nuevo producto equivalente</h3>
                            <button type="button" class="close rounded-pill" data-dismiss="modal" aria-label="Close">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                        <form class="form-horizontal" action="{{ route('kit.store-equivalente', $kit->id) }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row"> {{-- Producto --}}
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="hidden" name="kit_id" value="{{ $kit->id }}">
                                            <input type="hidden" name="kit_producto_id" id="kit_producto_id" value="">
                                            <label for="producto_id">Producto</label>
                                            <select name="producto_id" id="producto_id" class="select2 form-control {{ $errors->has('producto_id') ? 'is-invalid' : '' }}" data-placeholder="Seleccione un producto" data-validation-required-message="Este campo es obligatorio" required>
                                                <option value=""></option>
                                                @foreach($productos as $producto)
                                                <option value="{{ $producto->id }}" {{ old('producto_id', $producto->id) == $producto->id ? 'selected' : '' }}>
                                                    {{ $producto->producto }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-primary" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Close</span>
                                </button>
                                <button type="submit" class="btn btn-primary ml-1">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Accept</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // SELECT2 CATALOGO DE PRODUCTOS
        $('#producto_id').select2({
            placeholder: 'Seleccione un producto'
            , allowClear: true
        });
        // CAPTURA DEL ID CUANDO SE HABRE LA MODAL DE NUEVO EQUIVALENTE
        $('#modal-nuevo-equivalente').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botón que activó la modal
            // Usar .attr() para leer directamente los atributos HTML (más confiable)
            var kitProductoId = button.attr('data-kit-producto-id');
            var productoId = button.attr('data-producto-id');
            var unidades = button.attr('data-unidades');
            var modal = $(this);
            // Asignar los valores a los inputs hidden
            modal.find('#kit_producto_id').val(kitProductoId);
            // Debug: verificar que los valores se capturaron
            console.log('Valores capturados - kit_producto_id:', kitProductoId, 'producto_id:', productoId, 'unidades:', unidades);
        });
    });

</script>
@stop
