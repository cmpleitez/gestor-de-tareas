@extends('dashboard')

@section('contenedor')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Selección</h6>
            </div>
            <div class="card-content">
                <div class="card-body mt-2">
                    <form id="form-consultar" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Productos</h6>
                                <div class="form-group">
                                    <select name="producto_id" id="producto_id" class="select2 form-control {{ $errors->has('producto_id') ? 'is-invalid' : '' }}"
                                        data-validation-required-message="El producto es obligatorio" required>
                                        <option value="">Seleccione un producto...</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" data-codigo="{{ $producto->codigo }}" data-nombre="{{ $producto->producto }}">
                                                {{ $producto->codigo }} - {{ $producto->producto }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="help-block"></div>
                                    <div class="invalid-feedback"></div>
                                    @error('producto_id')
                                        <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                            {{ $errors->first('producto_id') }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Fecha inicial</h6>
                                <div class="form-group">
                                    <fieldset class="position-relative has-icon-left">
                                        <input type="text" name="fecha" id="filtro-fecha" class="form-control filtro-fecha-espanol {{ $errors->has('fecha') ? 'is-invalid' : '' }}" placeholder="Selecciona una fecha"
                                            data-validation-required-message="La fecha es obligatoria" required>
                                        <div class="form-control-position">
                                            <i class='bx bx-calendar'></i>
                                        </div>
                                    </fieldset>
                                    <div class="help-block"></div>
                                    <div class="invalid-feedback"></div>
                                    @error('fecha')
                                        <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                            {{ $errors->first('fecha') }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="consultar" class="btn btn-primary shadow">
                                    <i class="bx bx-search mr-50"></i> Consultar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">
                    Historial de transacciones
                </h6>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <ul class="widget-timeline mb-0">
                        <li class="timeline-items timeline-icon-primary active">
                            <div class="timeline-time">September, 16</div>
                            <h6 class="timeline-title">1983, orders, $4220</h6>
                            <p class="timeline-text">2 hours ago</p>
                            <div class="timeline-content">
                                <img src="{{ asset('app-assets/images/icon/pdf.png') }}" alt="document" height="23" width="19" class="mr-50">New Order.pdf
                            </div>
                        </li>
                        <li class="timeline-items timeline-icon-primary active">
                            <div class="timeline-time">September, 17</div>
                            <h6 class="timeline-title">12 Invoices have been paid</h6>
                            <p class="timeline-text">25 minutes ago</p>
                            <div class="timeline-content">
                                <img src="{{ asset('app-assets/images/icon/pdf.png') }}" alt="document" height="23" width="19" class="mr-50">Invoices.pdf
                            </div>
                        </li>
                        <li class="timeline-items timeline-icon-primary active pb-0">
                            <div class="timeline-time">September, 18</div>
                            <h6 class="timeline-title">Order #37745 from September</h6>
                            <p class="timeline-text">4 minutes ago</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        if (typeof jqBootstrapValidation === 'function') {
            $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
        }
        $('#form-consultar').on('submit', function(e) {
            e.preventDefault();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
            let producto_id = $('#producto_id').val();
            let $pickerInput = $('#filtro-fecha').pickadate('picker');
            let fecha = $pickerInput ? $pickerInput.get('select', 'yyyy-mm-dd') : $('#filtro-fecha').val();
            $.ajax({
                url: "{{ route('recepcion.lectura-transacciones') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    producto_id: producto_id,
                    fecha: fecha
                },
                success: function(response) {
                    toastr.success('Consulta procesada exitosamente');
                },
                error: function(xhr) {
                    console.error("Log:: [Usuario: {{ auth()->user()->name }}] Error en lecturaTransacciones:", xhr);
                    
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        toastr.warning(xhr.responseJSON.message || 'Revise los errores en el formulario');
                        if (errors.producto_id) {
                            $('#producto_id').addClass('is-invalid');
                            $('#producto_id').closest('.form-group').find('.invalid-feedback').text(errors.producto_id[0]).show();
                        }
                        if (errors.fecha) {
                            $('#filtro-fecha').addClass('is-invalid');
                            $('#filtro-fecha').closest('.form-group').find('.invalid-feedback').text(errors.fecha[0]).show();
                        }
                    } else {
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                            ? xhr.responseJSON.message 
                            : 'Error al procesar la consulta';
                        toastr.error(errorMessage, null, { "progressBar": true, "timeOut": 15000 });
                    }
                }
            });
        });
    });
</script>
@endsection
