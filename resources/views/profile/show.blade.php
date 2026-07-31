@extends('dashboard')

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            @livewire('profile.two-factor-authentication-form')
        </div>
    </div>
@endsection

