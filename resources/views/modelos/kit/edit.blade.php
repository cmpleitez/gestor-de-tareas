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
    .row-equivalentes {
        display: flex;
        flex-wrap: wrap;
    }
    .row-equivalentes > div[class*="col-"] {
        display: flex;
        flex-direction: column;
    }
    .row-equivalentes .card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .row-equivalentes .card-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .row-equivalentes .card-body {
        flex: 1;
    }
    .row-equivalentes .equivalente-card.equivalente-nuevo {
        border-radius: 4px !important;
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
                                    <img src="{{ Storage::disk('public')->url($kit->image_path) }}" alt="avatar" style="height: 22em; width: 54em; object-fit: cover;">
                                @else
                                    <img src="{{ asset('app-assets/images/pages/operador.png') }}" alt="avatar" style="height: 22em; width: 19em; object-fit: contain;">
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
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
                        <div class="row"> {{-- Productos --}}
                            @foreach ($kit->productos as $index => $producto)
                                @php
                                    $kitProducto_id = $producto->kitProductos->first()->id;
                                    $headingId = 'heading' . $kitProducto_id;
                                    $accordionId = 'accordion' . $kitProducto_id;
                                @endphp
                                <div class="row" style="margin-bottom: 0.5em; width: 1920px; max-width: 100%;">
                                    <div class="col-12 col-md-2 col-lg-2 col-xl-2">
                                        <div class="row">
                                            <div class="col-6"> {{-- Unidades --}}
                                                <div class="form-group">
                                                    <input type="hidden" name="producto[{{ $kitProducto_id }}][producto_id]" value="{{ $producto->id }}">
                                                    <input style="text-align: center;" type="text" name="producto[{{ $kitProducto_id }}][unidades]" id="producto_{{ $kitProducto_id }}_unidades" class="form-control {{ $errors->has('producto.' . $kitProducto_id . '.unidades') ? 'is-invalid' : '' }}" value="{{ old('producto.' . $kitProducto_id . '.unidades', $producto->kitProductos->first()->unidades) }}" data-validation-required-message="Este campo es obligatorio" data-validation-containsnumber-regex="^[1-9]\d*$" data-validation-containsnumber-message="Solo números positivos (mínimo 1)" required>
                                                    <div class="help-block"></div>
                                                    @error('producto.' . $kitProducto_id . '.unidades')
                                                        <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 text-center"> {{-- Botón de nuevo equivalente --}}
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-nuevo-equivalente" data-kit-producto-id="{{ $kitProducto_id }}" data-producto-id="{{ $producto->id }}" style="width: 100%; max-width: 100%; box-sizing: border-box;">
                                                    <i class="bx bx-plus" style="top:0.1em !important; left:-0.6em !important;">e</i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-10 col-lg-10 col-xl-10"> {{-- Titulo del producto --}}
                                        <div class="accordion collapse-icon accordion-icon-rotate">
                                            <div class="collapse-header" style="background-color:rgb(255, 255, 255) !important; min-height: 2em;">
                                                <div id="{{ $headingId }}" class="acordion-header" data-toggle="collapse" data-target="#{{ $accordionId }}" aria-expanded="false" aria-controls="{{ $accordionId }}" role="tablist">
                                                    {{ $producto->producto }}
                                                </div>
                                                <div id="{{ $accordionId }}" role="tabpanel" aria-labelledby="{{ $headingId }}" class="collapse">
                                                    <div class="row row-equivalentes"> {{-- Equivalentes --}}
                                                        @foreach ($producto->kitProductos->first()->equivalentes as $equivalente)
                                                            <div class="col-sm-6 col-md-2 col-lg-2" style="padding-top: 0.5em;">
                                                                <div class="card equivalente-card" data-producto-id="{{ $equivalente->producto_id }}" style="border: none !important;">
                                                                    <div class="card-content">
                                                                        <img class="card-img-top img-fluid" src="{{ asset('app-assets/images/pages/operador.png') }}" alt="Producto alterno" />
                                                                        <div class="card-body" style="padding: 0.5em; text-align: justify;">
                                                                            <small class="text-muted">{{ $equivalente->producto->producto }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer" style="background-color: #ffffffff !important; border-top: 0.5px solid rgb(233, 236, 240) !important; padding: 0 !important; text-align: center;">
                                                                        <a href="{{ Route('kit.destroy-equivalente', ['kit_producto_id' => $equivalente->kit_producto_id, 'producto_id' => $equivalente->producto_id, 'kit_id' => $equivalente->kit_id]) }}" class="btn btn-icon btn-sm">
                                                                            <i class="fa fa-trash text-warning-dark"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
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
                                        <div class="form-group"> {{-- Catálogo de selección de productos --}}
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
                                <button type="button" class="btn btn-light-warning" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Close</span>
                                </button>
                                <button type="submit" class="btn btn-warning ml-1">
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
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
        
        $('#modal-nuevo-equivalente').on('show.bs.modal', function(event) { //Asigna valores a los campos de la modal
            var button = $(event.relatedTarget);
            var kitProductoId = button.attr('data-kit-producto-id');
            var productoId = button.attr('data-producto-id');
            var modal = $(this);
            modal.find('#kit_producto_id').val(kitProductoId);
            modal.data('producto-id-excluir', productoId);
        });
        $('#modal-nuevo-equivalente').on('shown.bs.modal', function() { //Inicializa select2 y filtra el producto base
            var modal = $(this);
            var selectProducto = modal.find('#producto_id');
            var productoIdExcluir = modal.data('producto-id-excluir');
            if (selectProducto.hasClass('select2-hidden-accessible')) {
                selectProducto.select2('destroy');
            }
            if (productoIdExcluir) {
                selectProducto.find('option[value="' + productoIdExcluir + '"]').prop('disabled', true).hide();
            }
            selectProducto.select2({
                placeholder: 'Seleccione un producto'
                , allowClear: true
                , dropdownParent: modal
            });
        });
        $('#modal-nuevo-equivalente').on('hidden.bs.modal', function() { //Destruye select2 y restaura el producto base
            var modal = $(this);
            var selectProducto = modal.find('#producto_id');
            var productoIdExcluir = modal.data('producto-id-excluir');
            if (selectProducto.hasClass('select2-hidden-accessible')) {
                selectProducto.select2('destroy');
            }
            if (productoIdExcluir) {
                selectProducto.find('option[value="' + productoIdExcluir + '"]').prop('disabled', false).show();
            }
            selectProducto.val('').trigger('change');
            modal.find('#kit_producto_id').val('');
            modal.removeData('producto-id-excluir');
        });
        $('#modal-nuevo-equivalente form').on('submit', function() { //Memoriza el kit_producto_id y producto_id antes de enviar el formulario
            var kitProductoId = $('#kit_producto_id').val();
            var productoId = $('#producto_id').val();
            if (kitProductoId) {
                sessionStorage.setItem('openAccordion', kitProductoId);
            }
            if (productoId) {
                sessionStorage.setItem('nuevoEquivalenteProductoId', productoId);
            }
        });
        var accordionToOpen = sessionStorage.getItem('openAccordion'); //Abre el acordión del producto equivalente recien agregado
        var nuevoEquivalenteProductoId = sessionStorage.getItem('nuevoEquivalenteProductoId');
        if (accordionToOpen && nuevoEquivalenteProductoId) {
            sessionStorage.removeItem('openAccordion');
            setTimeout(function() {
                var headingId = '#heading' + accordionToOpen;
                var headingElement = $(headingId);
                if (headingElement.length) {
                    var accordionId = '#accordion' + accordionToOpen;
                    var accordionElement = $(accordionId);
                    var intentos = 0;
                    var maxIntentos = 20;
                    var aplicarEfecto = function() {
                        intentos++;
                        if (nuevoEquivalenteProductoId) {
                            var equivalenteCard = accordionElement.find('.equivalente-card[data-producto-id="' + nuevoEquivalenteProductoId + '"]');
                            if (equivalenteCard.length) {
                                equivalenteCard.addClass('equivalente-nuevo');
                                equivalenteCard[0].style.removeProperty('border');
                                equivalenteCard[0].style.setProperty('border-width', '2px', 'important');
                                equivalenteCard[0].style.setProperty('border-style', 'solid', 'important');
                                equivalenteCard[0].style.setProperty('border-radius', '4px', 'important');
                                var isBright = false;
                                var glowInterval = setInterval(function() {
                                    if (isBright) {
                                        equivalenteCard[0].style.setProperty('border-color', '#1d7949', 'important');
                                        equivalenteCard[0].style.setProperty('box-shadow', '0 0 8px rgba(29, 121, 73, 0.5), 0 0 15px rgba(29, 121, 73, 0.3)', 'important');
                                    } else {
                                        equivalenteCard[0].style.setProperty('border-color', '#4ade80', 'important');
                                        equivalenteCard[0].style.setProperty('box-shadow', '0 0 20px rgba(74, 222, 128, 0.9), 0 0 35px rgba(74, 222, 128, 0.6), 0 0 50px rgba(74, 222, 128, 0.4)', 'important');
                                    }
                                    isBright = !isBright;
                                }, 750);
                                equivalenteCard.data('glow-interval', glowInterval);
                                sessionStorage.removeItem('nuevoEquivalenteProductoId');
                                $('html, body').animate({
                                    scrollTop: equivalenteCard.offset().top - 100
                                }, 500);
                            } else if (intentos < maxIntentos) {
                                setTimeout(aplicarEfecto, 200);
                            }
                        }
                    };
                    if (accordionElement.hasClass('show') || accordionElement.hasClass('in')) {
                        aplicarEfecto();
                    } else {
                        accordionElement.one('shown.bs.collapse', function() {
                            setTimeout(aplicarEfecto, 100);
                        });
                        headingElement.trigger('click');
                    }
                }
            }, 500);
        }
        $(document).on('hidden.bs.collapse', '.collapse', function() { //Remueve el efecto de incandescencia cuando se cierra el acordión
            $(this).find('.equivalente-card.equivalente-nuevo').each(function() {
                var glowInterval = $(this).data('glow-interval');
                if (glowInterval) {
                    clearInterval(glowInterval);
                    $(this).removeData('glow-interval');
                }
                $(this).removeClass('equivalente-nuevo').css({
                    'border': 'none',
                    'border-radius': '',
                    'box-shadow': ''
                }).attr('style', 'border: none !important;');
            });
        });
    });
</script>
@stop
