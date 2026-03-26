@extends('dashboard')

@section('css')
<style>
    .widget-timeline .timeline-items {
        transition: background-color 0.3s ease;
        padding: 0.8rem !important;
        border-radius: 0.5rem;
    }
    .widget-timeline .timeline-items:hover {
        background-color: #e9f2fcff;
    }
</style>
@endsection

@section('contenedor')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Selección
                </div>
            </div>
            <div class="card-content">
                <div class="card-body mt-2">
                    <form id="form-consultar" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Productos</p>
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
                                <p>Fecha inicial</p>
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
                <div class="card-title">
                    Historial de transacciones
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <ul class="widget-timeline mt-2" id="lista-transacciones">
                        <!-- El historial se cargará aquí dinámicamente -->
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
        $('#producto_id, #filtro-fecha').on('change', function() { // Limpiar el historial al cambiar los filtros
            $('#lista-transacciones').empty();
        });
        $('#form-consultar').on('submit', function(e) {
            e.preventDefault();
            $('.invalid-feedback').text('').hide();
            $('#lista-transacciones').empty();
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
                    let transacciones = response.data_recibida.transacciones;
                    let $lista = $('#lista-transacciones');
                    $lista.empty();
                    if (transacciones.length > 0) {
                        transacciones.forEach(function(item) {
                            let dateObj = new Date(item.created_at);
                            let longDate = new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'long' }).format(dateObj);
                            function getRelativeTime(date) {
                                const now = new Date();
                                const diffInSeconds = Math.floor((now - date) / 1000);
                                if (diffInSeconds < 60) return 'hace un momento';
                                const diffInMinutes = Math.floor(diffInSeconds / 60);
                                if (diffInMinutes < 60) return `hace ${diffInMinutes} ${diffInMinutes === 1 ? 'minuto' : 'minutos'}`;
                                const diffInHours = Math.floor(diffInMinutes / 60);
                                if (diffInHours < 24) return `hace ${diffInHours} ${diffInHours === 1 ? 'hora' : 'horas'}`;
                                const diffInDays = Math.floor(diffInHours / 24);
                                if (diffInDays < 30) return `hace ${diffInDays} ${diffInDays === 1 ? 'día' : 'días'}`;
                                return longDate;
                            }
                            let relDate = getRelativeTime(dateObj);
                            let iconClass = item.tipo === 'entrada' ? 'timeline-icon-success' : 'timeline-icon-primary';
                            let textClass = item.tipo === 'entrada' ? 'text-success-dark' : 'text-primary';
                            let tipoLabel = item.tipo.charAt(0).toUpperCase() + item.tipo.slice(1);
                            let unitsFormatted = new Intl.NumberFormat('en-US').format(item.unidades);
                            let stockFormatted = new Intl.NumberFormat('en-US').format(item.stock_resultante);
                            let html = `
                                <li class="timeline-items ${iconClass} active">
                                    <div class="timeline-time text-capitalize">${relDate}</div>
                                    <h6 class="timeline-title"><b class="${textClass}">${unitsFormatted}</b> unidades -> Stock Resultante: <span style="font-size:1rem; font-weight:700;">${stockFormatted}</span> unidades</h6>
                                    <p class="timeline-text text-muted">${longDate}</p>
                                    <div class="timeline-content">
                                        <span class="text-secondary">${tipoLabel} : ${item.movimiento}</span>
                                    </div>
                                </li>
                            `;
                            $lista.append(html);
                        });
                    } else {
                        $lista.append('<li class="timeline-items timeline-icon-warning active"><div class="timeline-content">No se encontraron movimientos para el criterio seleccionado.</div></li>');
                    }
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
