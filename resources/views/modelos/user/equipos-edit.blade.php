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
                                <p>EQUIPOS DE {{ strtoupper($user->name) }}</p>
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
                <form action="{{ route('user.equipos-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div style="display: flex; flex-wrap: wrap; align-items: flex-start;">
                                @foreach ($equipos as $equipo)
                                    <div style="display: flex; flex-direction: column; align-items: center; margin-right: 10px;">
                                        <div style="background-color: #fdf7f7; border-top-left-radius: 50px; 
                                        border-bottom-left-radius: 50px; border-top-right-radius: 5px; 
                                        border-bottom-right-radius: 20px; width: 5rem; display: flex; 
                                        align-items: center; margin-right: 2rem; border: 1px solid rgb(213, 216, 216);">
                                            <div class="badge-circle badge-circle-md badge-circle-primary"
                                                style="margin-left: -0.2rem;">
                                                <i class="bx bxs-group"></i>
                                            </div>
                                            <div class="form-check form-check-inline" style="margin-right: 0; padding-left: 10px;">
                                                @if ($user->equipos->contains($equipo->id))
                                                    <input type="checkbox" name="equipos[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0; transform: scale(1.2);" id="{{ $loop->index }}" 
                                                    value="{{ $equipo->id }}" checked>
                                                @else
                                                    <input type="checkbox" name="equipos[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0; transform: scale(1.2);" id="{{ $loop->index }}"
                                                    value="{{ $equipo->id }}">
                                                @endif
                                            </div>
                                        </div>
                                        <div style="text-align: center; word-wrap: break-word; max-width: 100px; font-size: 0.8rem;">
                                            {{ $equipo->equipo }}
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
