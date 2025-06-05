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
                                <p>USUARIO {{ $user->name }}</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('user') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <form class="form-horizontal" action="{{ route('user.update', $user->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">

                                {{-- OFICINA --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="oficina_id">Oficina</label>
                                        <select class="select2 form-control" id="oficina_id" name="oficina_id">
                                            @foreach ($oficinas as $oficina)
                                                @if ($oficina->id == $user->oficina_id)
                                                    <option value="{{ $oficina->id }}" selected>{{ $oficina->oficina }}
                                                    </option>
                                                @else
                                                    <option value="{{ $oficina->id }}">{{ $oficina->oficina }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- CORREO --}}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="correo@ejemplo.com" required
                                                data-validation-required-message="El correo electrónico es requerido"
                                                value="{{ old('email', $user->email) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- GUARDAR --}}
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop

