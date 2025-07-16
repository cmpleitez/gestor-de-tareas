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
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Gestor de tareas {{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/ico/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/dragula.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-analytics.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}">
    <!-- END: Page CSS-->

    <style>
        html body.navbar-sticky .app-content .content-wrapper {
            padding: 3.8rem 2.2rem 0;
            margin-top: 3rem;
        }

        body {
            margin: 0;
            font-family: "IBM Plex Sans", Helvetica, Arial, serif;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.4;
            color: #727E8C;
            text-align: left;
            background-color: #F2F4F4;
        }

        .row {
            margin-right: 0;
            margin-left: 0;
        }

        p {
            margin: 0 0 0 0;
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #FFFFFF;
            background-clip: border-box;
            border-radius: 0.267rem;
            border: 0.5px solid #dadddf !important;
            box-shadow: 0 0 0 0.2px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8fafc;
            border-bottom: 0.5px solid rgb(236, 237, 240) !important;
            font-weight: 600;
            padding: 1rem 1rem !important;
            font-size: 0.8rem;
        }

        #heading5 {
            background: linear-gradient(156deg, #221627 0%, #4e2a5d 100%) !important;
            min-height: 52px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
        }

        .card-footer {
            background-color: #f0f5f9;
            border-top: 0.5px solid rgb(233, 236, 240) !important;
            padding: 1rem 1rem !important;
        }

        .button_keys {
            color: #28a745 !important;
            border: 1px solid #28a745 !important;
            font-size: 1.1rem !important;
            border-radius: 0.2rem !important;
            padding: 0.3rem 0.3rem 0 0.2rem;
            margin-right: 0.1rem;
        }

        .button_show {
            color: #216cf7 !important;
            border: 1px solid #216cf7 !important;
            font-size: 1.1rem !important;
            border-radius: 0.2rem !important;
            padding: 0.3rem 0.3rem 0 0.3rem;
            margin-right: 0.1rem !important;
        }

        .button_edit {
            color: rgb(255, 113, 57) !important;
            border: 1px solid rgb(255, 113, 57) !important;
            font-size: 1.1rem !important;
            border-radius: 0.2rem !important;
            padding: 0.3rem 0.3rem 0 0.3rem;
            margin-right: 0.1rem;
        }

        .button_delete {
            color: rgb(250, 99, 120) !important;
            border: 1px solid rgb(255, 58, 84) !important;
            font-size: 1.1rem !important;
            border-radius: 0.2rem !important;
            padding: 0.3rem 0.3rem 0 0.3rem;
            margin-right: 0.1rem;
        }

        div.dataTables_wrapper div.dataTables_filter,
        div.dataTables_wrapper div.dataTables_length {
            margin: 0rem 0;
        }

        .table.dataTable {
            margin-top: 1rem !important;
            margin-bottom: 2rem !important;
        }

        .table thead {
            text-transform: uppercase;
            font-size: .6rem;
        }

        .table thead th {
            padding-top: 0.4rem;
            padding-bottom: 0.3rem;
            background-color: rgb(243, 244, 250);
            color: #333;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
            padding: 0.3rem;
        }

        .table tfoot {
            text-transform: uppercase;
            font-size: .8rem;
        }

        .zero-configuration tfoot th {
            padding-top: 0.4rem;
            padding-bottom: 0.3rem;
            background-color: rgb(243, 244, 250);
            color: #333;
            vertical-align: middle;
        }

        .table.dataTable thead .sorting:before,
        .table.dataTable thead .sorting_asc:before,
        .table.dataTable thead .sorting_desc:before,
        .table.dataTable thead .sorting_desc_disabled:before {
            font-size: 1.3rem;
            top: -3px;
        }

        .table.dataTable thead .sorting:after,
        .table.dataTable thead .sorting_asc:after,
        .table.dataTable thead .sorting_desc:after,
        .table.dataTable thead .sorting_desc_disabled:after {
            font-size: 1.3rem;
            top: -14px;
        }

        .toast-info {
            background-color: rgb(29, 152, 235) !important;
            color: #fff !important;
            /* Texto blanco para mejor contraste */
        }

        .toast-success {
            background-color: rgb(14, 155, 73) !important;
            color: #fff !important;
        }

        .toast-error {
            background-color: rgb(233, 87, 71) !important;
            color: #fff !important;
        }

        .toast-warning {
            background-color: rgb(247, 132, 66) !important;
            color: #fff !important;
        }

        .toast-progress {
            background-color: rgba(255, 255, 255, .5) !important;
        }
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
                                                .then(() => {
                                                    toastr.success('Correo copiado al portapapeles');
                                                })
                                                .catch(err => {
                                                    toastr.error('Error al copiar el correo: ' + err.message, 'Error');
                                                    fallbackCopyTextToClipboard(text);
                                                });
                                        }

                                        function fallbackCopyTextToClipboard(text) {
                                            const textArea = document.createElement("textarea");
                                            textArea.value = text;
                                            textArea.style.top = "0";
                                            textArea.style.left = "0";
                                            textArea.style.position = "fixed";
                                            document.body.appendChild(textArea);
                                            textArea.focus();
                                            textArea.select();
                                            try {
                                                const successful = document.execCommand('copy');
                                                if (successful) {
                                                    toastr.success('Correo copiado al portapapeles', '', {
                                                        positionClass: 'toast-top-center'
                                                    });
                                                } else {
                                                    toastr.error('No se pudo copiar el correo', 'Error', {
                                                        positionClass: 'toast-top-center'
                                                    });
                                                }
                                            } catch (err) {
                                                toastr.error('No se pudo copiar el correo', 'Error', {
                                                    positionClass: 'toast-top-center'
                                                });
                                            }
                                            document.body.removeChild(textArea);
                                        }
                                    </script>
                                    <span
                                        class="user-status text-gray-600">{{ auth()->check() ? 'Conectado' : 'Desconectado' }}</span>
                                </div>
                                <div class="avatar">
                                    @if (auth()->check())
                                        @php $photoPath = auth()->user()->profile_photo_path; @endphp
                                        @if ($photoPath && Storage::disk('public')->exists($photoPath))
                                            <img src="{{ Storage::url($photoPath) }}" alt="avatar"
                                                style="height: 45px; width: 45px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('app-assets/images/pages/operador.png') }}"
                                                alt="avatar" style="height: 45px; width: 45px; object-fit: cover;">
                                        @endif
                                    @endif
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pb-0">
                                <a class="dropdown-item" href="#"><i class="bx bx-user mr-50"></i>
                                    Perfil</a>
                                <a class="dropdown-item" href="{{ route('recepcion.solicitudes') }}"><i
                                        class="bx bx-check-square mr-50"></i>
                                    Mis tareas</a>
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
                                style="width: 2.5rem; height: 2rem; object-fit: contain;">
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
                <li class=" nav-item"><a href="#"><i class="bx bx-menu"></i><span class="menu-title"
                            data-i18n="Menu Levels">Servicios</span></a>
                    <ul class="menu-content" style="display: block;">
                        @role('Beneficiario')
                            <li><a href="{{ Route('recepcion.create') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Recepciones</span>
                                </a></li>
                        @endrole
                        @role('Operador')
                            <li><a href="#">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Disponible</span>
                                </a></li>
                        @endrole
                        @role('Administrador')
                            <li>
                                <a href="#">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Administración</span>
                                </a>
                                <ul class="menu-content" style="display: block;">
                                    <li><a href="{{ Route('equipo') }}">
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="menu-item" data-i18n="Third Level">Equipos</span></a>
                                    </li>
                                    <li><a href="#">
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="menu-item" data-i18n="Third Level">Disponible</span>
                                        </a></li>
                                </ul>
                            </li>
                        @endrole
                        @role('SuperAdmin')
                            <li><a href="#">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Configuración</span>
                                </a>
                                <ul class="menu-content" style="display: block;">
                                    <li><a href="{{ Route('user') }}">
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="menu-item" data-i18n="Third Level">Usuarios</span>
                                        </a></li>
                                    <li><a href="{{ Route('equipo') }}">
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="menu-item" data-i18n="Third Level">Equipos</span>
                                        </a></li>
                                    <li><a href="{{ Route('tarea') }}">
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="menu-item" data-i18n="Third Level">Tareas</span>
                                        </a></li>
                                    <li><a href="{{ Route('solicitud') }}">
                                            <i class="bx bx-right-arrow-alt"></i>
                                            <span class="menu-item" data-i18n="Third Level">Solicitudes</span>
                                        </a></li>
                                </ul>
                            </li>
                        @endrole
                    </ul>
                </li>
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
                    class="bx bxl-slack pink mx-50 font-small-3"></i>Gestor de Tareas
                {{ config('app.version') }}</span>
            <span class="float-right d-sm-inline-block d-none">
                <a href="#" target="_blank">San Salvador</a> &copy; 2025
            </span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i
                    class="bx bx-up-arrow-alt"></i></button>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- <script src="/app-assets/vendors/js/charts/apexcharts.min.js"></script> -->
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
    <script src="/app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/footer.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- <script src="/app-assets/js/scripts/pages/dashboard-analytics.js"></script> -->
    <script src="{{ asset('app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/extensions/toastr.js') }}"></script>

    <!-- END: Page JS-->

    <!-- ... otros scripts ... -->
    <script>
        $(document).ready(function() {
            //Captura de alertas del backend
            @if (Session::has('success'))
                toastr.success("{{ Session::get('success') }}");
            @endif

            @if (Session::has('error'))
                toastr.error("{{ Session::get('error') }}", '', {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: true
                });
            @endif

            @if (Session::has('warning'))
                toastr.warning("{{ Session::get('warning') }}");
            @endif

            @if (Session::has('info'))
                toastr.info("{{ Session::get('info') }}");
            @endif

            // Inicializar DataTable
            if ($.fn.DataTable) {
                var table = $('.zero-configuration').DataTable({
                    "language": {
                        "url": "/app-assets/Spanish.json"
                    },
                    "responsive": true,
                    "autoWidth": false,
                    "order": [
                        [0, 'asc'],
                        [1, 'asc'],
                        [2, 'asc']
                    ],
                    "pageLength": 50, // Mostrar 50 registros por defecto
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "Todos"]
                    ] // Opciones de paginación
                });
                // Inicializar tooltips de Bootstrap 4
                $('[data-toggle="tooltip"]').tooltip();
                // Reinicializar tooltips después de cada evento de DataTables
                table.on('draw', function() {
                    $('[data-toggle="tooltip"]').tooltip();
                });
            } else {
                console.error('DataTables no está disponible');
            }

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
