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
    <title>Gestor de Tareas {{ config('app.version') }}</title>
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
                <!-- two factor challenge page start -->
                <section id="auth-two-factor" class="row flexbox-container">
                    <div class="col-xl-6 col-11 p-0 m-0">
                        <div class="card bg-authentication p-0 m-0">
                            <div class="row m-0 p-0">
                                <!-- left section-challenge -->
                                <div class="col-md-6 col-12 px-0">
                                    <div class="card disable-rounded-right m-0 d-flex justify-content-center">
                                        <div class="d-flex justify-content-center mt-3">
                                            <img src="{{ asset('app-assets/images/logo/logo.svg') }}" alt="Logo"
                                                style="height: 16vh; max-height: 400px; width: auto;">
                                        </div>
                                        <div class="card-header mb-3 mt-1 p-0">
                                            <div class="card-title">
                                                <h6 class="text-center">VERIFICACIÓN EN DOS PASOS</h6>
                                            </div>
                                        </div>
                                        <div class="card-content m-0">
                                            <div class="card-body">
                                                <form method="POST" action="{{ route('two-factor.login') }}">
                                                    @csrf

                                                    {{-- Código de la aplicación de autenticación --}}
                                                    <div class="form-group mb-50" id="bloque-codigo">
                                                        <label class="text-bold-600" for="code">Código de
                                                            autenticación</label>
                                                        <input type="text" class="form-control" id="code"
                                                            placeholder="000000" name="code" inputmode="numeric"
                                                            autofocus autocomplete="one-time-code">
                                                        <small class="text-muted">Ingrese el código que muestra la
                                                            aplicación de autenticación de su teléfono.</small>
                                                    </div>

                                                    {{-- Código de recuperación --}}
                                                    <div class="form-group mb-50 d-none" id="bloque-recuperacion">
                                                        <label class="text-bold-600" for="recovery_code">Código de
                                                            recuperación</label>
                                                        <input type="text" class="form-control" id="recovery_code"
                                                            placeholder="xxxxxxxxxx-xxxxxxxxxx" name="recovery_code"
                                                            autocomplete="one-time-code">
                                                        <small class="text-muted">Ingrese uno de los códigos de
                                                            recuperación que guardó al habilitar la verificación.</small>
                                                    </div>

                                                    <div
                                                        class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                                                        <div class="text-right">
                                                            <a href="#" class="card-link" id="usar-recuperacion"><small>¿Perdió
                                                                    su teléfono?</small></a>
                                                            <a href="#" class="card-link d-none"
                                                                id="usar-autenticacion"><small>Usar el código de la
                                                                    aplicación</small></a>
                                                        </div>
                                                    </div>

                                                    <button type="submit"
                                                        class="btn btn-primary glow w-100 position-relative">Verificar<i
                                                            id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
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
                <!-- two factor challenge page ends -->

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

    <!-- BEGIN: Application JavaScript -->
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/footer.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/extensions/toastr.js') }}"></script>
    <!-- END: Application JavaScript -->

    <script>
        $(function() {
            function alternar(mostrarRecuperacion) { // Se vacía el campo que se oculta: Fortify decide la vía por la presencia de recovery_code
                $('#bloque-codigo').toggleClass('d-none', mostrarRecuperacion);
                $('#bloque-recuperacion').toggleClass('d-none', !mostrarRecuperacion);
                $('#usar-recuperacion').toggleClass('d-none', mostrarRecuperacion);
                $('#usar-autenticacion').toggleClass('d-none', !mostrarRecuperacion);
                $(mostrarRecuperacion ? '#code' : '#recovery_code').val('');
                $(mostrarRecuperacion ? '#recovery_code' : '#code').focus();
            }

            $('#usar-recuperacion').on('click', function(e) {
                e.preventDefault();
                alternar(true);
            });

            $('#usar-autenticacion').on('click', function(e) {
                e.preventDefault();
                alternar(false);
            });

            @if ($errors->has('recovery_code'))
                alternar(true); // Se conserva la vía que falló para que el usuario reintente donde estaba
            @endif
        });
    </script>

    @if ($errors->has('code') || $errors->has('recovery_code'))
        <script>
            toastr.error(@json($errors->first('code') ?: $errors->first('recovery_code')), '', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: true
            });
        </script>
    @endif

</body>
<!-- END: Body-->

</html>
