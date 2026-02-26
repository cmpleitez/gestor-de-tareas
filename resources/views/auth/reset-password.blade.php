<!DOCTYPE html>
<html class="loading" lang="es" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Gestor de tareas te apoya en la administración de las tareas diarias de tu organización manteniendo una fluidez excepcional y eleva la calidad de la atención al cliente.">
    <meta name="keywords" content="gestión, tareas, calidad, control, fluidez, atención al cliente">
    <meta name="author" content=".">
    <title>Restablecer Clave - Gestor de Tareas</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">

    <!-- BEGIN: All CSS-->
    <link href="{{ asset('app-assets/vendors/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/colors.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/pages/authentication.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}" rel="stylesheet">
    <!-- END: All CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body
    class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 1-column  navbar-sticky footer-static bg-full-screen-image  blank-page blank-page"
    data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- reset password page start -->
                <section class="row flexbox-container">
                    <div class="col-xl-6 col-11 p-0 m-0">
                        <div class="card bg-authentication p-0 m-0">
                            <div class="row m-0 p-0">
                                <!-- left section-reset-password -->
                                <div class="col-md-6 col-12 px-0">
                                    <div class="card disable-rounded-right m-0 d-flex justify-content-center" style="height: 100%; min-height: 480px;">
                                        <div class="d-flex justify-content-center mt-3">
                                            <img src="{{ asset('app-assets/images/logo/logo.svg') }}" alt="Logo"
                                                style="height: 16vh; max-height: 400px; width: auto;">
                                        </div>
                                        <div class="card-header mb-1 mt-0 p-0">
                                            <div class="card-title">
                                                <h6 class="text-center">GESTOR DE TAREAS</h6>
                                            </div>
                                        </div>
                                        <div class="card-content m-0">
                                            <div class="card-body">
                                                <div class="text-muted text-center mb-3">
                                                    <small>Ingresa tu nueva clave para acceder a tu cuenta.</small>
                                                </div>

                                                <form method="POST" action="{{ route('password.update') }}">
                                                    @csrf
                                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                                    <div class="form-group mb-2">
                                                        <label class="text-bold-600" for="email">Correo electrónico</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <label class="text-bold-600" for="password">Nueva Clave</label>
                                                        <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <label class="text-bold-600" for="password_confirmation">Confirmar Clave</label>
                                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                                    </div>

                                                    <button type="submit" class="btn btn-primary glow w-100 position-relative mb-2 mt-2">
                                                        Restablecer Clave <i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- right section image -->
                                <div class="col-md-6 p-0 m-0">
                                    <img src="{{ asset('app-assets/images/pages/login.jpg') }}" alt="branding logo"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- reset password page ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- jQuery y dependencias principales -->
    <script src="{{ asset('app-assets/js/core/libraries/jquery.min.js') }}"></script>

    <!-- BEGIN: Critical JavaScript (Emergency Load) -->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/ui/unison.min.js') }}"></script>
    <!-- END: Critical JavaScript (Emergency Load) -->

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}", '', {
                    timeOut: 5000,
                    extendedTimeOut: 1000,
                    closeButton: true
                });
            @endforeach
        </script>
    @endif

</body>
<!-- END: Body-->
</html>

