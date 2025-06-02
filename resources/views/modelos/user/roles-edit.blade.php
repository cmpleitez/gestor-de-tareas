@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row">
        <div class="col-md-12">
            <div class="tarjeta">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 class="card-title">USUARIO {{ $user->name }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            <p class="card-text"></p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{!! route('user') !!}">
                                <i class="bx bx-arrow-back font-large-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('user.roles-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div style="display: flex; flex-wrap: wrap; align-items: center;">
                                @foreach ($roles as $role)
                                    <div style="display: flex; flex-direction: column; align-items: center; margin-right: 10px;">
                                        <div style="background-color: #f1f0f0; border-top-left-radius: 50px; 
                                        border-bottom-left-radius: 50px; border-top-right-radius: 5px; 
                                        border-bottom-right-radius: 20px; width: 5rem; display: flex; 
                                        align-items: center;">
                                            <div class="badge-circle badge-circle-md badge-circle-@php
                                                $class = 'secondary'; // Valor por defecto
                                                if ($role->id == 1) {
                                                    $class = 'danger';
                                                } elseif ($role->id == 2) {
                                                    $class = 'warning';
                                                } elseif ($role->id == 3) {
                                                    $class = 'info';
                                                }
                                                echo $class; @endphp"
                                                style="margin-left: -0.2rem;">
                                                <i class="{!! $role->icon !!}"></i>
                                            </div>

                                            <div class="form-check form-check-inline" style="margin-right: 0; padding-left: 10px;">
                                                @if ($user->hasRole($role->name))
                                                    <input type="checkbox" name="roles[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0;" id="{{ $loop->index }}" 
                                                    value="{{ $role->name }}" checked>
                                                @else
                                                    <input type="checkbox" name="roles[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0;" id="{{ $loop->index }}" 
                                                    value="{{ $role->name }}">
                                                @endif
                                            </div>

                                        </div>
                                        <div style="text-align: center; word-wrap: break-word; max-width: 100px;">
                                            {{ $role->name }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Otorgar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop




@section('js')
@stop
