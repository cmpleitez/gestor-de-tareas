<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Gestor de tareas">
    <meta name="keywords" content="gestor, gestión, tareas, actividades, proyectos">
    <meta name="author" content="">
    
    <title>Gestor de tareas</title>

    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/semi-dark-layout.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/pages/authentication.css">
    <!-- END: Page CSS-->
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body>
    
    <div class="card shadow-lg">
        <div class="row">
            <div class="col-md-6"> 
                <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                    <div class="card-header pb-1">
                        <div class="card-logo text-center">
                            <img class="fill" src="/app-assets/images/logo/logo.png" alt="Logo" style="width: 200px; height: auto;">
                        </div>
                        <div class="card-title">
                            <h6 class="text-center mt-2">GESTOR DE TAREAS</h6>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group mb-50">
                                    <label class="text-bold-600" for="exampleInputEmail1">Correo electrónico</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email address" name="email" required autofocus autocomplete="username"></div>
                                <div class="form-group">
                                    <label class="text-bold-600" for="exampleInputPassword1">Clave</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password" required autocomplete="current-password">
                                </div>
                                <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                                    <div class="text-left">
                                        <div class="checkbox checkbox-sm">
                                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                            <label class="checkboxsmall" for="exampleCheck1"><small>Manténme sesionado</small></label>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="card-link">
                                                <small>¿Olvidó su clave?</small>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary glow w-100 position-relative">Entrar<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                            </form>
                            <div class="text-center"><small class="mr-25">¿No tienes una cuenta?</small><a href="{{ route('register') }}"><small>Registrarse</small></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img src="/app-assets/images/pages/login.jpg" alt="branding logo">
            </div>
        </div>
    </div>

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js"></script>
    <script src="/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js"></script>
    <script src="/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <script src="/app-assets/js/scripts/components.js"></script>
    <script src="/app-assets/js/scripts/footer.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>