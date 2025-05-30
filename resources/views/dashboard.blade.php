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

    <link rel="shortcut icon" type="image/svg+xml" href="/app-assets/images/ico/favicon.svg">


    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/dragula.min.css">
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
    <link rel="stylesheet" type="text/css" href="/app-assets/css/pages/dashboard-analytics.css">
    <!-- END: Page CSS-->

    <style>
        .button_keys {
            color: #28a745 !important;
            border: 1px solid #28a745 !important;
            font-size: 1.1rem !important;
            margin-right: 0.12rem !important;
            padding: 0.1rem 0.35rem !important;
            border-radius: 0.2rem !important;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .button_show {
            color: #0fb100 !important;
            border: 1px solid #0fb100 !important;
            font-size: 1.1rem !important;
            margin-right: 0.12rem !important;
            padding: 0.1rem 0.35rem !important;
            border-radius: 0.2rem !important;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }

        .button_edit {
            color: #ff6629 !important;
            border: 1px solid #ff6629 !important;
            font-size: 1.1rem !important;
            margin-right: 0.12rem !important;
            padding: 0.1rem 0.35rem !important;
            border-radius: 0.2rem !important;
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }

        .button_delete {
            color: #ff3a72 !important;
            border: 1px solid #ff3a72 !important;
            font-size: 1.1rem !important;
            margin-right: 0.12rem !important;
            padding: 0.1rem 0.35rem !important;
            border-radius: 0.2rem !important;
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }



/* 
        .zero-configuration {
            font-size: 0.75rem;
            vertical-align: middle;
        }

        .zero-configuration.table-hover tbody tr:hover {
            background-color: rgb(233, 235, 250);
        }

        .zero-configuration td {
            padding: 0.2rem;
            margin: 0.2rem;
        } */


/*         .table thead th {
            color: #475F7B;
            border-top: none;
            vertical-align: middle;
            font-size: .7rem;
            padding: 1rem;
        }      */   

/*         .zero-configuration thead th {
            background-color:rgb(253, 232, 232); /* Color de fondo del encabezado */
            color: #333; /* Color del texto del encabezado */
            font-weight: bold; /* Grosor de la fuente */
            padding: 10px; /* Padding interno del encabezado */
            vertical-align: middle; /* Alineación vertical */
            border-bottom: 2px solid #dee2e6; /* Borde inferior, opcional */
        } */
    </style>

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
                                    data-toggle="tooltip" data-placement="top" title="Email"><i
                                        class="ficon bx bx-envelope"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html"
                                    data-toggle="tooltip" data-placement="top" title="Chat"><i
                                        class="ficon bx bx-chat"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html"
                                    data-toggle="tooltip" data-placement="top" title="Todo"><i
                                        class="ficon bx bx-check-circle"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calendar.html"
                                    data-toggle="tooltip" data-placement="top" title="Calendar"><i
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
                                    <span
                                        class="user-status text-muted">{{ auth()->check() ? 'Conectado' : 'Desconectado' }}</span>
                                </div>
                                <span>
                                    <img class="round" src="{{ Storage::url(auth()->user()->profile_photo_path) }}"
                                        alt="avatar" height="40" width="40">
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pb-0">
                                <a class="dropdown-item" href="page-user-profile.html"><i
                                        class="bx bx-user mr-50"></i> Edit Profile</a>
                                <a class="dropdown-item" href="app-todo.html"><i
                                        class="bx bx-check-square mr-50"></i> Task</a>
                                <div class="dropdown-divider mb-0"></div>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bx bx-power-off mr-50"></i> Logout
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

    <!-- Puedes ponerlo donde quieras mostrar el SVG, por ejemplo, en el header -->
    <div class="brand-logo">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="37.8"
            height="29.8" viewBox="0 0 378 298">
            <image id="Capa_2" data-name="Capa 2" x="14" y="14" width="352" height="262"
                xlink:href="data:img/png;base64,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" />
        </svg>
    </div>
    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto open">
                    <a class="navbar-brand align-items-baseline" href="{{ route('dashboard') }}">
                        <div class="brand-logo">
                            <img src="{{ asset('app-assets/images/ico/logo.ico') }}" alt="logo"
                                style="width: 2.5rem; height: 2rem; object-fit: contain;">
                        </div>
                        <h2 class="brand-text mb-0">Tareas</h2>
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
                <li class=" nav-item"><a href="#"><i class="bx bx-menu"></i><span class="menu-title"
                            data-i18n="Menu Levels">Opciones</span></a>
                    <ul class="menu-content" style="display: block;">
                        <li><a href="#"><i class="bx bx-right-arrow-alt"></i><span class="menu-item"
                                    data-i18n="Second Level">Mis tareas</span></a>
                        </li>
                        @role('Administradores')
                            <li><a href="#"><i class="bx bx-right-arrow-alt"></i><span class="menu-item"
                                        data-i18n="Second Level">Administración</span></a>
                                <ul class="menu-content" style="display: block;">
                                    <li><a href="{{ Route('equipo') }}"><i class="bx bx-right-arrow-alt"></i><span
                                                class="menu-item" data-i18n="Third Level">Equipos</span></a>
                                    </li>
                                    <li><a href="#"><i class="bx bx-right-arrow-alt"></i><span class="menu-item"
                                                data-i18n="Third Level">Disponible</span></a>
                                    </li>
                                </ul>
                            </li>
                        @endrole
                        @role('SuperAdmin')
                            <li><a href="#"><i class="bx bx-right-arrow-alt"></i><span class="menu-item"
                                        data-i18n="Second Level">Configuración</span></a>
                                <ul class="menu-content" style="display: block;">
                                    <li><a href="{{ Route('user') }}"><i class="bx bx-right-arrow-alt"></i><span
                                                class="menu-item" data-i18n="Third Level">Usuarios</span></a>
                                    </li>
                                    <li><a href="#"><i class="bx bx-right-arrow-alt"></i><span class="menu-item"
                                                data-i18n="Third Level">Disponible</span></a>
                                    </li>
                                </ul>
                            </li>
                        @endrole
                    </ul>
                </li>
                <li class=" navigation-header"><span>Support</span>
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
        <p class="clearfix mb-0"><span class="float-left d-inline-block">2019 &copy; PIXINVENT</span><span
                class="float-right d-sm-inline-block d-none">Crafted with<i
                    class="bx bxs-heart pink mx-50 font-small-3"></i>by<a class="text-uppercase"
                    href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent</a></span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i
                    class="bx bx-up-arrow-alt"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <script src="/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js"></script>
    <script src="/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js"></script>
    <script src="/app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="/app-assets/vendors/js/extensions/dragula.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <script src="/app-assets/js/scripts/components.js"></script>
    <script src="/app-assets/js/scripts/footer.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/pages/dashboard-analytics.js"></script>
    <!-- END: Page JS-->


    <!-- ... otros scripts ... -->
    <script>
        $(document).ready(function() {
            var table = $('.zero-configuration').DataTable();
            // Inicializar tooltips de Bootstrap 4
            $('[data-toggle="tooltip"]').tooltip();
            // Reinicializar tooltips después de cada evento de DataTables
            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
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
