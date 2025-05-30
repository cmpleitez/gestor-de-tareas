@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-10">
                            <h5 class="card-title">USUARIO {{$user->name}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            <p class="card-text"></p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{!! route('user') !!}">
                                <i class="fa-solid fa-circle-arrow-left fa-2xl"></i>
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('user.roles-update', ['user'=>$user->id]) }}" method="POST">
                        @csrf

                        <div class="row p-3">
                            <div class="col-md-12">
                                @foreach ($roles as $role)
                                    <div class="form-check form-check-inline p-3">
                                        @if ($user->hasRole($role->name))
                                            <input type="checkbox" name="roles[]" class="form-check-input" id="{{$loop->index}}" value="{{$role->name}}" checked>
                                        @else
                                            <input type="checkbox" name="roles[]" class="form-check-input" id="{{$loop->index}}" value="{{$role->name}}">
                                        @endif
                                        <label class="form-check-label" for="checkInline1">
                                            {{$role->name}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row mt-4 float-end">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Otorgar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="card shadom-lg">
                <div class="card-header">
                    <h4 class="card-title">Card With Header And Footer</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <p class="card-text">
                            Gummies bonbon apple pie fruitcake icing biscuit apple pie jelly-o sweet roll. Toffee
                            sugar plum sugar plum jelly-o jujubes bonbon dessert carrot cake.
                        </p>
                    </div>
                    <img class="img-fluid" src="../../../app-assets/images/slider/11.png" alt="Card image cap">
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <span>Card Footer</span>
                    <button class="btn btn-light-primary">Read More</button>
                </div>
            </div>
        </div>
    </div>    

@stop




@section('js')
@stop
