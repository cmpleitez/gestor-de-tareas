<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Gestor de Tareas">
    <meta name="keywords" content="">
    <meta name="author" content="Carlos Pleitez - cpleitez.2@gmail.com">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Titulo de la aplicación --}}
    <title>{{ auth()->user()->mainRole->name }} - ALFA.{{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/icons/favicon-32x32.png') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/icons/favicon-32x32.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">

    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="{{ asset('app-assets/css/fontawesome-zay.min.css') }}">

    <!-- BEGIN: Vendor CSS-->
    <link href="{{ asset('app-assets/vendors/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/animate/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/extensions/dragula.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/colors.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/pages/dashboard-analytics.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}" rel="stylesheet">
    <!-- END: All CSS-->

    <!-- BEGIN: Gestor de Tareas CSS-->
    <link href="{{ asset('app-assets/css/dashboard.css') }}" rel="stylesheet">
    <!-- END: Gestor de Tareas CSS-->

    @section('css')
    @show
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 2-columns navbar-sticky footer-static"
    data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    <!-- BEGIN: Header-->
    <div class="header-navbar-shadow"></div>
    <nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                    class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                        class="ficon bx bx-menu"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav bookmark-icons">
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html"
                                    data-toggle="tooltip" data-placement="top" data-title="Email"><i
                                        class="ficon bx bx-envelope"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html"
                                    data-toggle="tooltip" data-placement="top" data-title="Chat"><i
                                        class="ficon bx bx-chat"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html"
                                    data-toggle="tooltip" data-placement="top" data-title="Todo"><i
                                        class="ficon bx bx-check-circle"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calendar.html"
                                    data-toggle="tooltip" data-placement="top" data-title="Calendar"><i
                                        class="ficon bx bx-calendar-alt"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav">
                            <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i
                                        class="ficon bx bx-star warning"></i></a>
                                <div class="bookmark-input search-input">
                                    <div class="bookmark-input-icon"><i class="bx bx-search primary"></i></div>
                                    <input class="form-control input" type="text" placeholder="Explore Frest..."
                                        tabindex="0" data-search="template-list">
                                    <ul class="search-list"></ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <ul class="nav navbar-nav float-right">
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                                    class="ficon bx bx-fullscreen"></i></a></li>
                        <li class="dropdown dropdown-user nav-item">
                            <a class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                data-toggle="dropdown">
                                <div class="user-nav d-sm-flex d-none">
                                    <span class="user-name">{{ auth()->user()->name }}</span>
                                    <span class="user-status text-gray-600 d-flex align-items-center"
                                        onclick="copyToClipboard(event, '{{ auth()->user()->email }}')">
                                        <i class="bx bx-copy" style="cursor: pointer; padding-right: 0.5rem;"></i>
                                        <span
                                            class="hover:text-gray-900 !important transition-colors duration-200">{{ auth()->user()->email }}</span>
                                    </span>
                                    <script>
                                        function copyToClipboard(event, text) {
                                            event.stopPropagation();
                                            if (!navigator.clipboard) { // Verificar si la API del portapapeles está disponible
                                                fallbackCopyTextToClipboard(text); // Fallback para navegadores que no soportan clipboard API
                                                return;
                                            }
                                            navigator.clipboard.writeText(text)
                                                .then(function() {
                                                    if (typeof toastr !== 'undefined') {
                                                        toastr.success('Correo copiado al portapapeles');
                                                    }
                                                })
                                                .catch(function(err) {
                                                    if (typeof toastr !== 'undefined') {
                                                        toastr.error('Error al copiar el correo: ' + err.message, 'Error');
                                                    }
                                                    fallbackCopyTextToClipboard(text);
                                                });
                                        }

                                        function fallbackCopyTextToClipboard(text) {
                                            var textArea = document.createElement("textarea");
                                            textArea.value = text;
                                            textArea.style.top = "0";
                                            textArea.style.left = "0";
                                            textArea.style.position = "fixed";
                                            document.body.appendChild(textArea);
                                            textArea.focus();
                                            textArea.select();
                                            try {
                                                var successful = document.execCommand('copy');
                                                if (successful) {
                                                    if (typeof toastr !== 'undefined') {
                                                        toastr.success('Correo copiado al portapapeles', '', {
                                                            positionClass: 'toast-top-center'
                                                        });
                                                    }
                                                } else {
                                                    if (typeof toastr !== 'undefined') {
                                                        toastr.error('No se pudo copiar el correo', 'Error', {
                                                            positionClass: 'toast-top-center'
                                                        });
                                                    }
                                                }
                                            } catch (err) {
                                                if (typeof toastr !== 'undefined') {
                                                    toastr.error('No se pudo copiar el correo', 'Error', {
                                                        positionClass: 'toast-top-center'
                                                    });
                                                }
                                            }
                                            document.body.removeChild(textArea);
                                        }
                                    </script>
                                    <span class="user-status" style="color: #0056b3; font-weight: 600;">
                                        @if (auth()->check())
                                            Conectado como
                                            {{ auth()->user()->mainRole->name }}
                                        @else
                                            <span style="color: #d90429; font-weight: 600;">Desconectado</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="avatar">
                                    @if (auth()->check())
                                        @php $photoPath = auth()->user()->profile_photo_path; @endphp
                                        @if ($photoPath && Storage::disk('public')->exists($photoPath))
                                            <img src="{{ Storage::url($photoPath) }}" alt="avatar"
                                                style="height: 45px; width: 45px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('app-assets/images/pages/operador.png') }}"
                                                alt="avatar"
                                                style="height: 45px; width: 45px; object-fit: contain;">
                                        @endif
                                    @endif
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pb-0">
                                <a class="dropdown-item" href="#"><i class="bx bx-user mr-50"></i>
                                    Perfil</a>
                                <a class="dropdown-item" href="{{ route('recepcion.solicitudes') }}"><i
                                        class="bx bx-check-square mr-50"></i>
                                    Mis ordenes de compra</a>
                                <div class="dropdown-divider mb-0"></div>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bx bx-power-off mr-50"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto open">
                    <a class="navbar-brand align-items-baseline" href="{{ route('dashboard') }}">
                        <div class="brand-logo">
                            <img src="{{ asset('app-assets/images/icons/favicon-32x32.png') }}" alt="logo"
                                style="width: 2.2rem; height: 2.2rem; object-fit: fill;">
                        </div>
                        <h2 class="brand-text mb-0"></h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                            class="bx bx-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                            class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                            data-ticon="bx-disc"></i></a></li>
            </ul>
        </div>

        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation"
                data-icon-style="">

                @role('superadmin')
                    <li class=" nav-item"><a href="#"><i class="bx bx-shield"></i><span class="menu-title"
                                data-i18n="Menu Levels">Seguridad</span></a>
                        <ul class="menu-content" style="display: block;">
                            <li><a href="{{ Route('security.parametros') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Configuración</span>
                                </a></li>

                            <li><a href="{{ Route('security.index') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Dashboard</span>
                                </a></li>
                            <li><a href="{{ Route('security.events') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Eventos</span>
                                </a></li>
                            <li><a href="{{ Route('security.threat-intelligence') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Inteligencia de Amenazas</span>
                                </a></li>
                            <li><a href="{{ Route('security.ip-reputation') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Reputación de IPs</span>
                                </a></li>
                            <li><a href="{{ Route('security.logs') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Logs</span>
                                </a></li>
                        </ul>
                    </li>
                @endrole

                @role('admin|superadmin')
                    <li class=" nav-item"><a href="#"><i class="bx bx-cog"></i><span class="menu-title"
                                data-i18n="Menu Levels">Administración</span></a>
                        <ul class="menu-content" style="display: block;">
                            <li><a href="{{ Route('user') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Usuarios</span>
                                </a></li>
                            <li><a href="{{ Route('equipo') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Equipos</span>
                                </a></li>
                            <li><a href="{{ Route('tarea') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Tareas</span>
                                </a></li>
                            <li><a href="{{ Route('solicitud') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Solicitudes</span>
                                </a></li>
                        </ul>
                    </li>
                @endrole

                @role('cliente')
                    <li class=" nav-item"><a href="#"><i class="bx bx-menu"></i><span class="menu-title"
                                data-i18n="Menu Levels">Servicios</span></a>
                        <ul class="menu-content" style="display: block;">
                            <li><a href="{{ Route('recepcion.create') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Recepciones</span>
                                </a></li>
                            <li><a href="{{ route('producto') }}">
                                    <i class="bx bx-package"></i>
                                    <span class="menu-item" data-i18n="Second Level">Catálogo</span>
                                </a></li>
                        </ul>
                    </li>
                @endrole

                <li class=" navigation-header"><span>Soporte</span>
                </li>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">

                @section('contenedor')
                @show

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-left d-inline-block"><i
                    class="bx bxl-slack pink mx-50 font-small-3"></i>ALFA.{{ config('app.version') }}</span>
            <span class="float-right d-sm-inline-block d-none">
                <a href="#" target="_blank">San Salvador</a> &copy; 2025
            </span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i
                    class="bx bx-up-arrow-alt"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- jQuery y dependencias principales -->

    <!-- BEGIN: Critical JavaScript (Emergency Load) -->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- END: Critical JavaScript (Emergency Load) -->

    <!-- BEGIN: Vendor JavaScript -->
    <!-- BEGIN: Theme JS (para tooltips) -->
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
    <!-- END: Theme JS -->
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/numeral/numeral.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/dragula.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/charts/chart.min.js') }}"></script>

    <!-- END: Vendor JavaScript -->

    <!-- ... otros scripts ... -->
    <script>
        // Configuración global de toastr para todo el proyecto
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "30000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $(document).ready(function() {
            //Captura de alertas del backend
            @if (Session::has('success'))
                toastr.success("{{ Session::get('success') }}");
            @endif
            @if (Session::has('error'))
                toastr.error("{{ Session::get('error') }}", '', {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: true,
                });
            @endif
            @if (Session::has('warning'))
                toastr.warning("{{ Session::get('warning') }}");
            @endif
            @if (Session::has('info'))
                toastr.info("{{ Session::get('info') }}");
            @endif
            @if (Session::has('danger'))
                toastr.error("{{ Session::get('danger') }}");
            @endif
        });
    </script>
    <!-- ... otros scripts ... -->

    <!-- Custom js for this page -->
    @section('js')
    @show
    <!-- End custom js for this page -->

</body>
<!-- END: Body-->

</html>
