@extends('dashboard')

@section('contenedor')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Verificación de Correo Electrónico</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success" role="alert">
                                ¡Se ha enviado un nuevo enlace de verificación a tu correo electrónico!
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-12 pt-1">
                                <p>Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu dirección de correo
                                    electrónico haciendo clic en el enlace que acabamos de enviarte? Si no recibiste el
                                    correo, con gusto te enviaremos otro.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Reenviar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
