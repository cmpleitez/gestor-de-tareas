@extends('dashboard')

@section('css')
<link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
@stop

@section('contenedor')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                    <div class="col-11 p-1">
                        <h4 class="card-title">KITS</h4>
                        <p class="card-text">Son grupos de productos y accesorios enfocados a resolver una necesidad específica</p>
                    </div>
                    <div class="col-1 d-flex justify-content-end" style="padding: 0;">
                        <a href="{!! route('kit.create') !!}">
                            <div class="badge-circle badge-circle-md btn-warning">
                                <i class="bx bx-plus-medical font-small-3"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive mt-1">
                        <table id="datatable" class="table zero-configuration table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Kit</th>
                                    <th class="text-center">Creado</th>
                                    <th class="text-center">Actualizado</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kits as $kit)
                                <tr>
                                    {{-- CAMPOS --}}
                                    <td class="text-center">{{ $kit->id }}</td>
                                    <td>{{ $kit->kit }}</td>
                                    <td class="text-center">{{ $kit->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $kit->updated_at->format('d/m/Y') }}</td>
                                    {{-- ACTIVAR --}}
                                    @can('autorizar')
                                    <td class="text-center">
                                        <form action="{{ route('kit.activate', $kit->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <div class="custom-control custom-switch"
                                                style="transform: scale(0.6); margin: 0;" data-toggle="tooltip"
                                                data-html="true" data-placement="bottom"
                                                title="<i class='bx bxs-error-circle'></i> {{ $kit->activo ? 'Desactivar' : 'Activar' }} {{ $kit->kit }}">
                                                <input id="activate_{{ $kit->id }}" type="checkbox"
                                                    class="custom-control-input" @if ($kit->activo) checked @endif
                                                onchange="this.form.submit();">
                                                <label class="custom-control-label"
                                                    for="activate_{{ $kit->id }}"></label>
                                            </div>
                                        </form>
                                    </td>
                                    @endcan
                                    {{-- TABLERO DE CONTROL --}}
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="label">
                                            {{-- EDITAR --}}
                                            @can('editar')
                                                <a href="{{ route('kit.asignar-productos', $kit->id) }}"
                                                    role="button" data-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-html="true" data-placement="bottom"
                                                    title="<i class='bx bxs-cog'></i> Asignar productos a {{ $kit->kit }}"
                                                    class="button_edit align-center border border-secondary-dark text-secondary-dark bg-secondary-light">
                                                    <i class="bx bxs-cog"></i>
                                                </a>

                                                <a href="{{ route('kit.edit', $kit->id) }}" role="button"
                                                    data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                    data-placement="bottom"
                                                    title="<i class='bx bxs-edit-alt'></i> Editar datos de {{ $kit->kit }}"
                                                    class="button_edit align-center border border-warning-dark text-warning-dark bg-warning-light">
                                                    <i class="bx bxs-edit-alt"></i>
                                                </a>
                                            @endcan
                                            {{-- ELIMINAR --}}
                                            @can('eliminar')
                                                <a href="{{ route('kit.destroy', $kit->id) }}" role="button"
                                                    data-toggle="tooltip" data-popup="tooltip-custom" data-html="true"
                                                    data-placement="bottom"
                                                    title="<i class='bx bxs-eraser'></i> Eliminar {{ $kit->kit }}"
                                                    class="button_delete align-center border border-danger-dark text-danger-dark bg-danger-light">
                                                    <i class="bx bxs-eraser"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Kit</th>
                                    <th class="text-center">Creado</th>
                                    <th class="text-center">Actualizado</th>
                                    @can('autorizar')
                                        <th class="text-center">Estado</th>
                                    @endcan
                                    <th class="text-center">Tablero de control</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

{{-- ORIENTACIÓN PARA TABLAS EN MÓVILES --}}
@include('components.orientation-manager')

<script>
        $(document).ready(function() {
            //INICIALIZACION DE DATATABLES
            if ($.fn.DataTable) {
                $('.zero-configuration').DataTable({
                    "language": { "url": "/app-assets/Spanish.json" },
                    "pageLength": 10,
                    "columnDefs": [
                        {
                            "targets": 0,
                            "type": "num"
                        }
                    ],                    
                });
            }
            // INICIALIZAR TOOLTIPS
            $('[data-toggle="tooltip"]').tooltip({
                html: true,
                placement: 'bottom'
            });
        });
</script>

@stop