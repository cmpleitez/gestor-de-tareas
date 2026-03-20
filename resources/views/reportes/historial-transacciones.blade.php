@extends('dashboard')

@section('contenedor')
<div class="row">
    <!-- Columna izquierda: Select2 -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Selección</h6>
            </div>
            <div class="card-content">
                <div class="card-body mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Productos</h6>
                            <div class="form-group">
                                <select id="producto_id" class="select2 form-control">
                                    <option value="">Seleccione un producto...</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" data-codigo="{{ $producto->codigo }}" data-nombre="{{ $producto->producto }}">
                                            {{ $producto->codigo }} - {{ $producto->producto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Fecha inicial</h6>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="text" id="filtro-fecha" class="form-control filtro-fecha-espanol" placeholder="Selecciona una fecha">
                                <div class="form-control-position">
                                    <i class='bx bx-calendar'></i>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" id="consultar" class="btn btn-primary shadow">
                                <i class="bx bx-search mr-50"></i> Consultar
                            </button>
                        </div>
                    </div>
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
        $('#consultar').on('click', function() {
            let producto_id = $('#producto_id').val();
            let fecha = $('#filtro-fecha').val();

            $.ajax({
                url: "{{ route('recepcion.historial-transacciones') }}",
                type: 'GET',
                data: {
                    producto_id: producto_id,
                    fecha: fecha
                },
                success: function(response) {
                    toastr.success('Consulta enviada al servidor');
                    console.log('Respuesta:', response);
                },
                error: function(xhr) {
                    toastr.error('Error al procesar la consulta');
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection
