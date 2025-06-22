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
                                <p>NUEVA SOLICITUD</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('recepcion') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
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
                                        <textarea class="form-control" id="textarea-counter" name="detalles" rows="3" required minlength="9"
                                            maxlength="255" data-validation-required-message="El campo detalles es requerido"
                                            data-validation-minlength-message="El campo detalles debe tener al menos 9 caracteres"
                                            data-validation-maxlength-message="El campo detalles debe tener menos de 255 caracteres"
                                            placeholder="Detalles de la solicitud (mínimo 9 caracteres)">{{ old('detalles') }}</textarea>
                                        <small class="counter-value float-right"><span class="char-count">0</span> /
                                            255</small>
                                        @error('detalles')
                                            <div class="col-sm-12 badge bg-danger text-wrap">
                                                {{ $errors->first('detalles') }}
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
                this.setCustomValidity('El campo detalles es requerido');
            } else if (this.validity.tooShort) {
                this.setCustomValidity('El campo detalles debe tener al menos 9 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('textarea-counter').addEventListener('input', function(e) {
            this.setCustomValidity('');
        });
    </script>
@stop
