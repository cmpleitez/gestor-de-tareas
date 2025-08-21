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
    <title>Gestor de tareas {{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/icons/favicon-32x32.png') }}">
    <link rel="shortcut icon" type="image/x-icon"
        href="{{ asset('app-assets/images/icons/favicon-32x32.png') }}">
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
                <!-- login page start -->
                <section id="auth-login" class="row flexbox-container">
                    <div class="col-xl-6 col-11 p-0 m-0">
                        <div class="card bg-authentication p-0 m-0">
                            <div class="row m-0 p-0">
                                <!-- left section-login -->
                                <div class="col-md-6 col-12 px-0">
                                    <div
                                        class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                        <div class="d-flex justify-content-center">
                                            <img src="{{ asset('app-assets/images/logo/logo.png') }}"
                                                alt="Logo" class="height-100" style="width: auto; object-fit: contain;">
                                        </div>
                                        <div class="card-header">
                                            <div class="card-title m-0 p-0">
                                                <h6 class="text-center">GESTOR DE TAREAS</h6>
                                            </div>
                                        </div>
                                        <div class="card-content m-0">
                                            <div class="card-body">
                                                <form method="POST" action="{{ route('login') }}">
                                                    @csrf
                                                    <div class="form-group mb-50">
                                                        <label class="text-bold-600" for="exampleInputEmail1">Correo
                                                            electrónico</label>
                                                        <input type="email" class="form-control" id="exampleInputEmail1"
                                                            placeholder="correo@ejemplo.com" name="email" required
                                                            autofocus autocomplete="username">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="text-bold-600"
                                                            for="exampleInputPassword1">Clave</label>
                                                        <input type="password" class="form-control"
                                                            id="exampleInputPassword1" placeholder="Clave"
                                                            name="password" required autocomplete="current-password">
                                                    </div>
                                                    <div
                                                        class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                                                        <div class="text-left">
                                                            <div class="checkbox checkbox-sm">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="exampleCheck1" name="remember">
                                                                <label class="checkboxsmall"
                                                                    for="exampleCheck1"><small>Mantener sesión
                                                                        iniciada</small></label>
                                                            </div>
                                                        </div>
                                                        <div class="text-right"><a
                                                                href="{{ route('password.request') }}"
                                                                class="card-link"><small>¿Olvidaste tu
                                                                    clave?</small></a>
                                                        </div>
                                                    </div>
                                                    <button type="submit"
                                                        class="btn btn-primary glow w-100 position-relative">Entrar<i
                                                            id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                                                </form>
                                                <hr>
                                                <div class="text-center"><small class="mr-25">¿No tienes una cuenta de
                                                        usuario?</small><a
                                                        href="{{ route('register') }}"><small>Regístrate</small></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- right section image -->
                                <div class="col-md-6 p-0 m-0">
                                    <img src="{{ asset('app-assets/images/pages/login.jpg') }}"
                                        alt="branding logo" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- login page ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- jQuery y dependencias principales -->
    <script src="{{ asset('resources/js/js/core/libraries/jquery.min.js') }}"></script>

    <!-- BEGIN: Critical JavaScript (Emergency Load) -->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/ui/unison.min.js') }}"></script>
    <!-- END: Critical JavaScript (Emergency Load) -->

    <!-- BEGIN: Application JavaScript -->
    <script src="{{ asset('resources/js/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('resources/js/js/core/app.js') }}"></script>
    <script src="{{ asset('resources/js/js/scripts/components.js') }}"></script>
    <script src="{{ asset('resources/js/js/scripts/footer.js') }}"></script>
    <script src="{{ asset('resources/js/js/scripts/extensions/toastr.js') }}"></script>
    <!-- END: Application JavaScript -->

    @if ($errors->has('email') || $errors->has('password'))
    <script>
        toastr.error("Las credenciales ingresadas son incorrectas", '', { 
                timeOut: 0, 
                extendedTimeOut: 0, 
                closeButton: true 
            });
    </script>
    @endif

</body>
<!-- END: Body-->

</html>