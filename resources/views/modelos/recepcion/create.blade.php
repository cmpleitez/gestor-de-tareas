@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <!-- CABECERA -->
                <div class="card-header">
                    <div class="row mt-1">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0rem;">
                            <div class="col-md-10 align-items-center" style="padding-left: 0.5rem;">
                                <p>NUEVA SOLICITUD</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('recepcion.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="card-content">
                        <div class="card-body">

                            <div class="row"> {{-- Solicitud --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="solicitud_id">Solicitud</label>
                                        <select class="select2 form-control" id="solicitud_id" name="solicitud_id">
                                            @foreach ($solicitudes as $solicitud)
                                                @if ($solicitud->id == $solicitud->id)
                                                    <option value="{{ $solicitud->id }}" selected>
                                                        {{ $solicitud->solicitud }}
                                                    </option>
                                                @else
                                                    <option value="{{ $solicitud->id }}">{{ $solicitud->solicitud }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('solicitud_id')
                                        <div class="col-sm-12 badge bg-danger text-wrap">
                                            {{ $errors->first('solicitud_id') }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row"> {{-- Detalles --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="textarea-counter">Detalles</label>
                                        <textarea class="form-control" id="textarea-counter" name="detalle" rows="3" required minlength="9"
                                            maxlength="255" data-validation-required-message="El campo detalle es requerido"
                                            data-validation-minlength-message="El campo detalle debe tener al menos 9 caracteres"
                                            data-validation-maxlength-message="El campo detalle debe tener menos de 255 caracteres"
                                            placeholder="Detalles de la solicitud (mínimo 9 caracteres)">{{ old('detalle') }}</textarea>
                                        <small class="counter-value float-right"><span class="char-count">0</span> /
                                            255</small>
                                        @error('detalle')
                                            <div class="col-sm-12 badge bg-danger text-wrap">
                                                {{ $errors->first('detalle') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- GUARDAR --}}
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('textarea-counter').addEventListener('invalid', function(e) {
            if (this.validity.valueMissing) {
                this.setCustomValidity('El campo detalle es requerido');
            } else if (this.validity.tooShort) {
                this.setCustomValidity('El campo detalle debe tener al menos 9 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('textarea-counter').addEventListener('input', function(e) {
            this.setCustomValidity('');
        });
    </script>
@stop
