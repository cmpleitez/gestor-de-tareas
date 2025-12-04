@extends('dashboard')

@section('css')

<style>
    .acordion-header {
        background-color: rgb(255, 255, 255) !important;
        min-height: 2em;
        font-size: 1.2em !important;
        padding-right: 3rem !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        position: relative;
        display: flex;
        align-items: center;
    }

</style>

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

                        <div class="row mb-2"> {{-- Kit --}}
                            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
                                @if ($kit->image_path && Storage::disk('public')->exists($kit->image_path))
                                <img src="{{ Storage::url($kit->image_path) }}" alt="avatar" style="height: 22em; width: 35em; object-fit: cover;">
                                @else
                                <img src="{{ asset('app-assets/images/pages/operador.png') }}" alt="avatar" style="height: 22em; width: 35em; object-fit: contain;">
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kit">Kit</label>
                                                <input type="text" name="kit" id="kit" class="form-control {{ $errors->has('kit') ? 'is-invalid' : '' }}" data-validation-required-message="Este campo es obligatorio" data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$" data-validation-containsnumber-message="Solo se permiten letras y paréntesis, sin espacios al inicio/final ni dobles espacios" data-validation-minlength-message="El nombre debe tener al menos 3 caracteres" data-clear="true" minlength="3" placeholder="Nombre del kit" value="{{ old('kit', $kit->kit) }}" required>
                                                <div class="help-block"></div>
                                                @error('kit')
                                                <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                    {{ $errors->first('kit') }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row"> {{-- Productos --}}
                            @foreach ($kit->productos as $index => $producto)
                                @php
                                    $kitProducto_id = $producto->kitProductos->first()->id;
                                    $headingId = 'heading' . $kitProducto_id;
                                    $accordionId = 'accordion' . $kitProducto_id;
                                @endphp
                                <div class="row" style="margin-bottom: 0.5em">
                                    <div class="col-12 col-md-1 text-center"> {{-- Unidades --}}
                                        <input type="hidden" name="producto[{{ $kitProducto_id }}][producto_id]" value="{{ $producto->id }}">
                                        <input style="text-align: center;" type="number" name="producto[{{ $kitProducto_id }}][unidades]" id="producto_{{ $kitProducto_id }}_unidades" class="form-control" value="{{ old('producto.' . $kitProducto_id . '.unidades', $producto->kitProductos->first()->unidades) }}" required>
                                    </div>
                                    <div class="col-12 col-md-1 text-center"> {{-- Botón de nuevo equivalente --}}
                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-nuevo-equivalente" data-kit-producto-id="{{ $kitProducto_id }}" style="width: 100%; max-width: 100%; box-sizing: border-box;">
                                            <i class="bx bx-plus" style="top:0.1em !important; left:-0.6em !important;">e</i>
                                        </button>
                                    </div>
                                    <div class="col-12 col-md-10"> {{-- Titulo del producto --}}
                                        <div class="accordion collapse-icon accordion-icon-rotate">
                                            <div class="collapse-header" style="background-color: #f8f9fa !important; min-height: 2em;">
                                                <div id="{{ $headingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $accordionId }}" aria-expanded="false" aria-controls="{{ $accordionId }}" role="tablist">
                                                    ID:{{ $kitProducto_id }} - Producto_id:{{ $producto->id }} - nombre:{{ $producto->producto }}
                                                </div>
                                                <div id="{{ $accordionId }}" role="tabpanel" aria-labelledby="{{ $headingId }}" class="collapse">
                                                    <div class="row"> {{-- Equivalentes --}}
                                                        @foreach ($producto->kitProductos->first()->equivalentes as $equivalente)
                                                        <div class="col-md-3 col-sm-6 mb-sm-1">
                                                            <div class="card">
                                                                <div class="card-content">
                                                                    <img class="card-img-top img-fluid" src="{{ asset('app-assets/images/pages/operador.png') }}" alt="Producto alterno" />
                                                                    <div class="card-body">
                                                                        <h4 class="card-title">ID:{{ $equivalente->kit_producto_id }} - Producto_id:{{ $equivalente->producto_id }} - nombre:{{ optional($equivalente->producto)->producto ?? 'N/A' }}</h4>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-end"> {{-- Botón guardar --}}
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
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group"> {{-- Producto --}}
                                            <input type="hidden" name="kit_id" value="{{ $kit->id }}">
                                            <input type="hidden" name="kit_producto_id" id="kit_producto_id" value="">
                                            <label for="producto_id">Producto</label>
                                            <select name="producto_id" id="producto_id" class="select2 form-control {{ $errors->has('producto_id') ? 'is-invalid' : '' }}" data-placeholder="Seleccione un producto" data-validation-required-message="Este campo es obligatorio" required>
                                                <option value=""></option>
                                                @foreach($productos as $producto)
                                                <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                                    {{ $producto->producto }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <div class="help-block"></div>
                                            @error('producto_id')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('producto_id') }}
                                            </div>
                                            @enderror
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
        // ASIGNA EL ID DE KIT_PRODUCTO A LA MODAL
        $('#modal-nuevo-equivalente').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var kitProductoId = button.attr('data-kit-producto-id');
            var modal = $(this);
            modal.find('#kit_producto_id').val(kitProductoId);
        });
        // INICIALIZA SELECT2
        $('#modal-nuevo-equivalente').on('shown.bs.modal', function() {
            var modal = $(this);
            var selectProducto = modal.find('#producto_id');
            if (selectProducto.hasClass('select2-hidden-accessible')) { // Si ya está inicializado, lo destruimos primero
                selectProducto.select2('destroy');
            }
            selectProducto.select2({ // Inicializamos Select2
                placeholder: 'Seleccione un producto'
                , allowClear: true
                , dropdownParent: modal // Importante: establece el contenedor padre
            });
        });
        // DESTRUYE SELECT2 CUANDO LA MODAL SE OCULTA
        $('#modal-nuevo-equivalente').on('hidden.bs.modal', function() {
            var modal = $(this); // Destruyendo select2
            var selectProducto = modal.find('#producto_id');
            if (selectProducto.hasClass('select2-hidden-accessible')) {
                selectProducto.select2('destroy');
            }
            selectProducto.val('').trigger('change'); // Limpiar valores
            modal.find('#kit_producto_id').val('');
        });
    });

</script>
@stop
