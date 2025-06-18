@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                            <div class="col-md-10 align-items-center" style="padding: 0 0 0 0;">
                                <p>TAREAS DE {{ strtoupper($solicitud->solicitud) }}</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('solicitud') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('solicitud.actualizar-tareas', ['solicitud' => $solicitud->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div style="display: flex; flex-wrap: wrap; align-items: flex-start;">
                                @foreach ($tareas as $tarea)
                                    <div style="display: flex; flex-direction: column; align-items: center; margin-right: 10px;">
                                        <div style="background-color: #fdf7f7; border-top-left-radius: 50px; 
                                        border-bottom-left-radius: 50px; border-top-right-radius: 5px; 
                                        border-bottom-right-radius: 20px; width: 10rem; display: flex; 
                                        align-items: center; margin-right: 2rem; border: 1px solid rgb(213, 216, 216);">
                                            <div class="badge-circle badge-circle-md badge-circle-primary"
                                                style="margin-left: -0.2rem;">
                                                <i class="bx bx-task"></i>
                                            </div>
                                            <div class="form-check form-check-inline" style="margin-right: 0; padding-left: 10px;">
                                                @if ($solicitud->tareas->contains($tarea->id))
                                                    <input type="checkbox" name="tareas[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0; transform: scale(1.2);" id="{{ $loop->index }}" 
                                                    value="{{ $tarea->id }}" checked>
                                                @else
                                                    <input type="checkbox" name="tareas[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0; transform: scale(1.2);" id="{{ $loop->index }}"
                                                    value="{{ $tarea->id }}">
                                                @endif
                                            </div>
                                        </div>
                                        <div style="text-align: justify; word-wrap: break-word; max-width: 100px; font-size: 0.8rem;">
                                            {{ $tarea->tarea }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
