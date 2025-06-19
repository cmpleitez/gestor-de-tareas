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
                            <a href="{!! route('envio') !!}">
                                <div class="badge badge-pill badge-primary">
                                    <i class="bx bx-arrow-back font-medium-3"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FORMULARIO -->
            <form class="form-horizontal" action="{{ route('tarea.store') }}" method="POST" novalidate>
                @csrf
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="solicitud_id">Solicitud</label>
                                    <select class="select2 form-control" id="solicitud_id" name="solicitud_id">
                                        @foreach ($solicitudes as $solicitud)
                                            @if ($solicitud->id == $solicitud->id)
                                                <option value="{{ $solicitud->id }}" selected>{{ $solicitud->solicitud }}
                                                </option>
                                            @else
                                                <option value="{{ $solicitud->id }}">{{ $solicitud->solicitud }}</option>
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

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Counter</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <p class="mb-2">There are times when we need the user to only enter a certain number of characters for it, we have the property counter, the value is a number and determines the maximum. Use <code>.char-textarea</code> with <code>&lt;textarea&gt;</code>tag for counting text-length.</p>
                                        <div class="row">
                                            <div class="col-12">
                                                <fieldset class="form-label-group mb-0">
                                                    <textarea data-length=20 class="form-control char-textarea" id="textarea-counter" rows="3" placeholder="Counter"></textarea>
                                                    <label for="textarea-counter">Counter</label>
                                                </fieldset>
                                                <small class="counter-value float-right"><span class="char-count">0</span> / 20 </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
@stop