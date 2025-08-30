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

    {{-- Titulo de la aplicación --}}
    <title>{{ auth()->user()->main_role }} - ALFA.{{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/icons/favicon-32x32.png') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/icons/favicon-32x32.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">

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

    <style>
        .avatar {
            margin: 0px;
        }

        .badge-pill {
            padding-right: 0.7rem;
            padding-left: 0.7rem;
        }

        .badge {
            font-size: 0.6rem;
        }

        .badge-primary {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        .badge-secondary {
            background-color: #6c757d !important;
            color: #ffffff !important;
        }

        .badge-success {
            background-color: #28a745 !important;
            color: #ffffff !important;
        }

        .badge-danger {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }

        .badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
            font-weight: 500 !important;
        }

        /* Clases para bordes de tarjetas según estado */
        .border-badge-secondary {
            border-left-color: #6c757d !important;
        }

        .border-badge-primary {
            border-left-color: #3498db !important;
        }

        .border-badge-success {
            border-left-color: #28a745 !important;
        }

        .border-badge-danger {
            border-left-color: #dc3545 !important;
        }

        .border-badge-warning {
            border-left-color: #ffc107 !important;
        }

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

        .selectable-item {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            padding: 20px;
            margin-bottom: 1rem;
            width: 304px;
            min-width: 304px;
            max-width: 304px;
            height: auto;
            min-height: 120px;
            flex-shrink: 0;
        }

        .selectable-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            border-color: #007bff;
        }

        .selectable-item.selected {
            border-color: #007bff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .selectable-item.selected:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            border-color: #007bff;
        }

        .selectable-item::before {
            /* Triángulo de color en esquina superior derecha */
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 20px 20px 0;
            border-color: transparent #221627 transparent transparent;
            border-radius: 0 8px 0 0;
        }

        /* Estilos para elementos internos de selectable-item */
        .selectable-item .item-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .selectable-item .item-info {
            flex: 1;
        }

        .selectable-item .item-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #2c3e50;
            margin-bottom: 4px;
            /* Manejo de texto multilínea con truncamiento */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
            line-height: 1.3;
            max-height: 3.9em;
            /* 3 líneas * 1.3 line-height */
        }

        .selectable-item .item-desc {
            font-size: 0.8rem;
            color: #6c757d;
            /* Manejo de texto con truncamiento */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
            line-height: 1.2;
            max-height: 1.92em;
            /* 2 líneas * 1.2 line-height */
        }

        /* Indicadores de selección */
        .radio-indicator {
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
        }

        .selectable-item.selected .radio-indicator {
            border-color: #007bff;
            background: #007bff;
        }

        .selectable-item.selected .radio-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* Checkbox indicator */
        .checkbox-indicator {
            width: 18px;
            height: 18px;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
        }

        .selectable-item.selected .checkbox-indicator {
            border-color: #007bff;
            background: #007bff;
        }

        .selectable-item.selected .checkbox-indicator::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
        }

        /* Radio indicator */
        .radio-indicator {
            width: 18px;
            height: 18px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
        }

        .selectable-item.selected .radio-indicator {
            border-color: #007bff;
            background: #007bff;
        }

        .selectable-item.selected .radio-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* Estilos específicos para formularios de edición */
        .card-body .selectable-item {
            width: 220px;
            min-width: 220px;
            max-width: 220px;
            padding: 15px;
            margin: 0;
            flex-shrink: 0;
        }

        .card-body .selectable-item .item-name {
            font-size: 0.9rem;
            -webkit-line-clamp: 2;
            max-height: 2.6em;
        }

        .card-body .selectable-item .item-desc {
            font-size: 0.75rem;
            -webkit-line-clamp: 1;
            max-height: 1.2em;
        }

        /* ===== CONTENEDOR PARA ELEMENTOS SELECCIONABLES ===== */

        .selectable-items-container {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 20px;
        }

        /* ===== CLASES ESPECÍFICAS PARA TAREAS ===== */

        .tareas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            padding: 1rem 0;
        }

        .tarea-card {
            height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .tarea-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .tarea-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #7367f0, #9c8cfc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .tarea-checkbox {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tarea-checkbox-input {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            transition: all 0.3s ease;
            background: white;
        }

        .tarea-card.selected .tarea-checkbox-input {
            border-color: #007bff;
            background: #007bff;
        }

        .tarea-content {
            flex: 1;
            font-weight: 600;
            font-size: 0.95rem;
            color: #2c3e50;
            margin-bottom: 4px;
            word-wrap: break-word;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }



        /* ===== ESTILOS PARA TOOLTIPS ===== */

        /* Tooltip base con fondo oscuro elegante */
        .tooltip-inner {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%) !important;
            color: #ffffff !important;
            font-family: 'IBM Plex Sans', sans-serif !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
            max-width: 250px !important;
        }

        /* Flecha del tooltip */
        .tooltip.bs-tooltip-top .arrow::before {
            border-top-color: #1a1a2e !important;
        }

        .tooltip.bs-tooltip-bottom .arrow::before {
            border-bottom-color: #1a1a2e !important;
        }

        .tooltip.bs-tooltip-left .arrow::before {
            border-left-color: #1a1a2e !important;
        }

        .tooltip.bs-tooltip-right .arrow::before {
            border-right-color: #1a1a2e !important;
        }

        /* ===== RESPONSIVE ===== */

        @media (max-width: 768px) {
            .tareas-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 0.75rem;
            }

            .tarea-card {
                height: 140px;
                padding: 1rem;
            }
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
                            <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <div class="user-nav d-sm-flex d-none">
                                    <span class="user-name">{{ auth()->user()->name }}</span>
                                    <span class="user-status text-gray-600 d-flex align-items-center"
                                        onclick="copyToClipboard(event, '{{ auth()->user()->email }}')">
                                        <i class="bx bx-copy" style="cursor: pointer; padding-right: 0.5rem;"></i>
                                        <span class="hover:text-gray-900 !important transition-colors duration-200">{{
                                            auth()->user()->email }}</span>
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
                                        {{ auth()->user()->main_role }}
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
                                    <img src="{{ asset('app-assets/images/pages/operador.png') }}" alt="avatar"
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

                    </ul>
                </li>
                @role('SuperAdmin')
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
                <li class=" nav-item"><a href="#"><i class="bx bx-shield"></i><span class="menu-title"
                            data-i18n="Menu Levels">Monitoreo de Seguridad</span></a>
                    <ul class="menu-content" style="display: block;">
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