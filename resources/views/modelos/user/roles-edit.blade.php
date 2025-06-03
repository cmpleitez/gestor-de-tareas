@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row">
        <div class="col-md-12">
            <div class="tarjeta">
                <div class="card-header pt-1">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0.3rem;">
                            <div class="col-md-11" style="padding-left: 0.5rem;">
                                <p class="card-title">USUARIO {{ $user->name }}</p>
                                <p class="card-subtitle mb-2 text-muted"></p>
                                <p class="card-text"></p>
                            </div>
                            <div class="col-md-1 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('user') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('user.roles-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div style="display: flex; flex-wrap: wrap; align-items: flex-start;">
                                @foreach ($roles as $role)
                                    <div style="display: flex; flex-direction: column; align-items: center; margin-right: 10px;">
                                        <div style="background-color: #fdf7f7; border-top-left-radius: 50px; 
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
                                                    style="margin-right: 0; margin-left: 0; transform: scale(1.2);" id="{{ $loop->index }}" 
                                                    value="{{ $role->name }}" checked>
                                                @else
                                                    <input type="checkbox" name="roles[]" class="form-check-input" 
                                                    style="margin-right: 0; margin-left: 0; transform: scale(1.2);" id="{{ $loop->index }}"
                                                    value="{{ $role->name }}">
                                                @endif
                                            </div>

                                        </div>
                                        <div style="text-align: center; word-wrap: break-word; max-width: 100px; font-size: 0.8rem;">
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
