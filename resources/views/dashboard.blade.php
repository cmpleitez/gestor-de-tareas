<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Gestor de Tareas">
    <meta name="keywords" content="">
    <meta name="author" content="Carlos Pleitez - cpleitez.2024@gmail.com">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Titulo de la aplicación --}}
    <title>{{ auth()->user()->mainRole->name }} - ALFA.{{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
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
    <link href="{{ asset('app-assets/css/plugins/forms/input-clear.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/pickers/daterange/daterangepicker.css') }}" rel="stylesheet">
    <!-- END: All CSS-->

    <!-- BEGIN: Gestor de Tareas CSS-->
    <link href="{{ asset('app-assets/css/dashboard.css') }}" rel="stylesheet">
    <!-- END: Gestor de Tareas CSS-->

    @section('css')
    @show
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 2-columns navbar-sticky footer-static menu-collapsed"
    data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    {{-- Pre-paint: en desktop anclado pinta la vista ya desplegada; en tablet/móvil nace como overlay oculto (sin repliegue visible del init) --}}
    <script>
        (function () {
            try {
                if (window.innerWidth >= 1200 && localStorage.getItem('sidebarEstado') === 'expanded') {
                    document.body.classList.remove('menu-collapsed');
                    document.body.classList.add('menu-expanded');
                } else if (window.innerWidth < 1200) {
                    document.body.classList.remove('vertical-menu-modern');
                    document.body.classList.add('vertical-overlay-menu', 'fixed-navbar', 'menu-hide');
                }
            } catch (e) {}
        })();
    </script>
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
                                            {{ strtoupper(auth()->user()->mainRole->name) }}
                                        @else
                                            <span style="color: #d90429; font-weight: 600;">Desconectado</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="avatar">
                                    @if (auth()->check())
                                        @php $photoPath = auth()->user()->image_path; @endphp
                                        @if ($photoPath && Storage::disk('public')->exists($photoPath))
                                            <img src="{{ Storage::disk('public')->url($photoPath) }}" alt="avatar"
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
                                @can('ver-solicitudes')
                                    <a class="dropdown-item" href="{{ route('tienda.solicitudes') }}">
                                        <i class="bx bx-check-square mr-50"></i>Solicitudes
                                    </a>
                                @endcan
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
        
        <div class="navbar-header" style="padding: 10px;">
            <ul class="nav navbar-nav flex-row">
                
                <li class="nav-item mr-auto open menu-toggle">
                    <a class="navbar-brand d-flex justify-content-center align-items-center">
                        <div class="brand-logo d-flex justify-content-center">
                            <img src="{{ asset('app-assets/images/logo/logo.svg') }}" alt="Logo" style="width: 85%; height: auto; max-width: 200px;">
                        </div>
                        <p class="brand-text mb-0" style="font-size: 1rem;">Gestor de Tareas</p>
                    </a>
                </li>

                <li class="nav-item nav-toggle menu-toggle">
                    <a class="nav-link pr-0" data-toggle="collapse">
                        <i class="bx bx-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                        <i class="toggle-icon bx bx-circle font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="bx-circle"></i>
                    </a>
                </li>

            </ul>
        </div>

        <div class="shadow-bottom"></div>
        <div class="main-menu-content mt-3">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation"
                data-icon-style="">
                @can('ver-catalogo')
                    <li class="nav-item has-sub nav-item-administracion"><a href="#"><i class="bx bxs-cog"></i><span class="menu-title"
                                data-i18n="Menu Levels">Administración</span></a>
                        <ul class="menu-content">
                            @can('ver-reportes')
                                <li><a href="{{ Route('reporte.indicadores-seguridad') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item" data-i18n="Second Level">Indicadores de seguridad</span>
                                    </a></li>
                            @endcan
                            <li><a href="{{ Route('parametro') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Configuración</span>
                                </a></li>
                            <li><a href="{{ Route('user') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Usuarios</span>
                                </a></li>
                            <li><a href="{{ Route('solicitud') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Solicitudes</span>
                                </a></li>
                            <li><a href="{{ Route('kit') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Kits</span>
                                </a></li>

                            <li class="dropdown-divider"></li>

                            <li><a href="{{ Route('marca') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Marcas</span>
                                </a></li>
                            <li><a href="{{ Route('modelo') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Modelos</span>
                                </a></li>
                            <li><a href="{{ Route('tipo') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Tipos</span>
                                </a></li>
                            <li><a href="{{ Route('producto') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item" data-i18n="Second Level">Productos</span>
                                </a></li>
                        </ul>
                    </li>
                @endcan

                <li class="nav-item has-sub nav-item-servicios mt-1"><a href="#"><i class="bx bx-list-ul"></i><span class="menu-title"
                            data-i18n="Menu Levels">Servicios</span></a>
                    <ul class="menu-content">
                        @can('crear-stock')
                            <li><a href="{{ route('recepcion.create-stock') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item" data-i18n="Second Level">Stocks</span>
                            </a></li>
                        @endcan
                        @can('ver-reportes')
                            <li><a href="{{ route('recepcion.historial-transacciones') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item" data-i18n="Second Level">Transacciones</span>
                            </a></li>
                        @endcan
                        @can('ver-tienda')
                            <li class="dropdown-divider"></li>
                            <li><a href="{{ Route('tienda') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item" data-i18n="Second Level">Tienda</span>
                            </a></li>
                        @endcan
                    </ul>
                </li>

                <li class=" navigation-header"><span>Soporte</span>
                </li>
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->
    {{-- Pre-paint: aplica el estado guardado de las categorías antes del primer render (sin jQuery, aún no está cargado) --}}
    <script>
        (function () {
            try {
                if (!(window.innerWidth >= 1200 && localStorage.getItem('sidebarEstado') === 'expanded')) return;
                var guardado = localStorage.getItem('categoriasAbiertas');
                if (guardado === null) return;                          // Sin estado guardado: se respeta el markup por defecto
                var abiertas = JSON.parse(guardado) || [];
                var lis = document.querySelectorAll('#main-menu-navigation li.has-sub');
                for (var i = 0; i < lis.length; i++) {
                    var titulo = lis[i].querySelector(':scope > a .menu-title');
                    var abierta = titulo && abiertas.indexOf(titulo.textContent.trim()) !== -1;
                    lis[i].classList[abierta ? 'add' : 'remove']('open');
                }
            } catch (e) {}
        })();
    </script>

    <!-- BEGIN: Content-->
    <div class="app-content content mt-0 ml-15 mr-0">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-body">
                @section('contenedor')
                @show
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>                                 <!-- Backdrop estándar Vuexy: en overlay-menu (tablet/móvil) oscurece el contenido y cierra el menú al tocar fuera -->

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
    <script src="{{ asset('app-assets/js/helpers.js') }}"></script>
    <!-- END: Theme JS -->
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/numeral/numeral.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/dragula.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/inputmask/jquery.inputmask.min.js') }}"></script>
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
    <script src="{{ asset('app-assets/js/scripts/forms/input-clear.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/input-masks.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/daterange/moment.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
    <!-- END: Vendor JavaScript -->

    <!-- ... otros scripts ... -->
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
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

            // Inicializar Inputmask globalmente para todos los inputs con atributos data-inputmask
            if (typeof $.fn.inputmask !== 'undefined') {
                $(":input[data-inputmask]").inputmask();
            }

            // Inicializar Select2 globalmente
            if (typeof $.fn.select2 !== 'undefined') {
                function formatSelect2Option(item) {
                    if (!item.id) {
                        return item.text;
                    }
                    var element = $(item.element);
                    var codigo = element.data('codigo');
                    var nombre = element.data('nombre');
                    
                    if (codigo && nombre) {
                        var $badge = $('<span class="badge bg-secondary-dark text-white font-weight-bold" style="font-size: 0.8rem; margin-right: 8px;"></span>').text(codigo);
                        var $text = $('<span></span>').text(nombre);
                        return $('<span></span>').append($badge).append($text);
                    }
                    return item.text;
                }

                $('.select2').select2({
                    width: '100%',
                    templateResult: formatSelect2Option,
                    templateSelection: formatSelect2Option,
                    language: {
                        noResults: function() {
                            return "No se encontraron resultados";
                        }
                    }
                });
            }

            // Inicializar Pick-a-date globalmente en español para cualquier reporte
            if (typeof $.fn.pickadate !== 'undefined') {
                $('.filtro-fecha-espanol').pickadate({
                    selectYears: true,
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd',
                    monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                    today: 'Hoy',
                    clear: 'Limpiar',
                    close: 'Cerrar'
                });
            }
        });
        // Expansión masiva para modo celular al abrir el menú desde la hamburguesa
        $(document).on('click', '.menu-toggle', function() {
            if (window.innerWidth < 1200) {
                setTimeout(function() {
                    if ($('body').hasClass('menu-open')) {
                        $('.main-menu').find('li.has-sub').not('.open').addClass('open').children('ul').hide().slideDown(200);
                    }
                }, 300);
            }
        });
        // MODO TÁCTIL TABLET HORIZONTAL (sidebar replegado): un toque en el ícono de una categoría despliega el sidebar y abre exactamente esa categoría; un toque fuera del sidebar repliega el sidebar y todas las categorías
        (function () {
            var activo = function () { // Solo dispositivo táctil, en ancho desktop (>=1200) y con el sidebar replegado (sin anclar)
                return window.matchMedia('(pointer: coarse)').matches && window.innerWidth >= 1200 && $('body').hasClass('menu-collapsed');
            };
            $(document).on('touchend', '.main-menu li.has-sub > a', function (e) {
                if (!activo()) return;
                e.preventDefault(); e.stopPropagation(); // Suprime el click y el hover emulados del toque: el estado deja de depender del mouse
                var $li = $(this).parent();
                $('.main-menu, .navbar-header').addClass('expanded'); // Despliega el sidebar y lo deja anclado hasta el toque fuera
                $('.main-menu li.has-sub.open').not($li).removeClass('open menu-collapsed-open').children('ul').slideUp(200, function () { $(this).css('display', ''); });
                if ($li.hasClass('open')) { // Segundo toque en la misma categoría: solo la repliega (el sidebar queda desplegado)
                    $li.removeClass('open menu-collapsed-open').children('ul').slideUp(200, function () { $(this).css('display', ''); });
                } else {
                    $li.addClass('open').children('ul').hide().slideDown(200, function () { $(this).css('display', ''); });
                }
            });
            $(document).on('touchstart', function (e) {
                if (!activo()) return;
                if ($(e.target).closest('.main-menu').length) return; // Toques dentro del sidebar no repliegan
                $('.main-menu, .navbar-header').removeClass('expanded'); // Repliega el sidebar
                $('.main-menu li.has-sub').removeClass('open menu-collapsed-open').children('ul').css('display', ''); // Y todas las categorías (sin memoria de reapertura)
            });
        })();
        // ANCLAJE PERSISTENTE: mientras el sidebar esté anclado, ignora todo repliegue automático de la plantilla (init, breakpoints, resize); solo el toggle manual del usuario puede replegar
        (function () {
            if (!(window.jQuery && $.app && $.app.menu && typeof $.app.menu.collapse === 'function')) return;
            var _collapseOriginal = $.app.menu.collapse; // Colapso real de la plantilla
            var _toggleManual = false; // true solo en el instante en que el usuario pulsa el toggle
            $(document).on('mousedown touchstart', '.menu-toggle, .modern-nav-toggle', function () { // Se dispara antes del click que ejecuta toggle()
                _toggleManual = true;
                setTimeout(function () { _toggleManual = false; }, 400);
            });
            $.app.menu.collapse = function (defMenu) {
                var anclado = window.innerWidth >= 1200 && localStorage.getItem('sidebarEstado') === 'expanded';
                if (anclado && !_toggleManual) { // Repliegue automático estando anclado: se ignora y se mantiene desplegado
                    $('body').removeClass('menu-collapsed').addClass('menu-expanded');
                    this.collapsed = false;
                    this.expanded = true;
                    return;
                }
                _collapseOriginal.call(this, defMenu);
            };
        })();
        // MEMORIA DE CATEGORÍAS (solo anclado): al navegar, cada categoría del menú queda exactamente como la dejó el usuario (abierta o cerrada)
        (function () {
            var anclado = function () { return window.innerWidth >= 1200 && localStorage.getItem('sidebarEstado') === 'expanded'; };
            var clave = function (li) { return $(li).children('a').find('.menu-title').text().trim(); }; // Identifica la categoría por su título
            window.addEventListener('pagehide', function () { // Al salir de la vista guarda el estado exacto de los submenús
                if (!anclado()) return;
                var abiertas = $('#main-menu-navigation li.has-sub.open').map(function () { return clave(this); }).get();
                localStorage.setItem('categoriasAbiertas', JSON.stringify(abiertas));
            });
            var restaurar = function () {
                if (!anclado()) return;
                var guardado = localStorage.getItem('categoriasAbiertas');
                if (guardado === null) return; // Primera carga sin estado guardado: se respeta el comportamiento por defecto
                var abiertas = [];
                try { abiertas = JSON.parse(guardado) || []; } catch (e) {}
                $('#main-menu-navigation li.has-sub').each(function () {
                    $(this).toggleClass('open', abiertas.indexOf(clave(this)) !== -1);
                });
            };
            $(window).on('load', function () { setTimeout(restaurar, 120); }); // Guardia silenciosa: si el init de la plantilla movió algo, repone el estado (si nada cambió, no toca el DOM)
        })();
        // ANCLAJE DEL SIDEBAR EN DESKTOP: recuerda el estado desplegado/replegado y lo reaplica al cargar cada vista
        $(document).on('click', '.menu-toggle, .modern-nav-toggle', function() { // Guarda la elección manual del usuario
            if (window.innerWidth >= 1200) {
                setTimeout(function() {
                    localStorage.setItem('sidebarEstado', $('body').hasClass('menu-expanded') ? 'expanded' : 'collapsed');
                }, 50);
            }
        });
        $(window).on('load', function() { // Se ejecuta tras el init de la plantilla (que fuerza replegado) y reaplica el anclado
            if (window.innerWidth >= 1200 && localStorage.getItem('sidebarEstado') === 'expanded') {
                if ($.app && $.app.menu && typeof $.app.menu.expand === 'function') {
                    $.app.menu.expand();
                }
            }
        });
        // SWITCH DE ÍCONO DEL TOGGLE (repuesto): bx-circle (replegado) ↔ bx-disc (desplegado)
        function actualizarIconoToggle() { // Alterna el ícono según el estado del sidebar
            var expandido = document.body.classList.contains('menu-expanded');
            $('.main-menu .navbar-header .collapse-toggle-icon')
                .toggleClass('bx-disc', expandido)
                .toggleClass('bx-circle', !expandido);
        }
        $(document).on('click', '.menu-toggle', function() { // Actualiza el ícono tras el toggle
            if (window.innerWidth >= 1200) setTimeout(actualizarIconoToggle, 60);
        });
        $(window).on('load', actualizarIconoToggle); // Estado inicial del ícono al cargar la vista
    </script>

    <!-- Custom js for this page -->
    @section('js')
    @show
    <!-- End custom js for this page -->

    <livewire:check-notifications />
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notification-received', (event) => {
                // Livewire 3 emite eventos con parámetros dentro de un array.
                const data = (Array.isArray(event) && event.length > 0) ? event[0] : (event.data || event);
                console.log("Log:: Notificación recibida en dashboard:", data);

                if (typeof toastr !== 'undefined' && data) {
                    const message = `
                        ${data.mensaje}
                        <br><br>
                        <button type="button" class="btn bg-primary-light text-dark btn-sm btn-block" style="width: 100%; margin-top: 10px; font-weight: bold;" onclick="$(this).closest('.toast').find('.toast-close-button').click()">Enterado</button>
                    `;
                    toastr[data.tipo || 'info'](message, data.titulo, {
                        "closeButton": true,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "timeOut": "0",
                        "extendedTimeOut": "0",
                        "tapToDismiss": false,
                        "preventDuplicates": false,
                        "enableHtml": true,
                    });
                }
            });
            // Consulta inmediata al cargar la página para notificaciones pendientes sin esperar el primer ciclo de 15s
            Livewire.dispatch('trigger-check-notifications');
        });
    </script>

    <script>
        // Polling inteligente: solo ejecuta cuando la pestaña es visible
        (function () {
            const INTERVALO_MS = 15000; // 15 segundos
            let intervalo = null;
            function consultarNotificaciones() {
                if (document.visibilityState === 'visible') {
                    Livewire.dispatch('trigger-check-notifications');
                }
            }
            function iniciarPolling() {
                if (!intervalo) {
                    intervalo = setInterval(consultarNotificaciones, INTERVALO_MS);
                }
            }
            function detenerPolling() {
                if (intervalo) {
                    clearInterval(intervalo);
                    intervalo = null;
                }
            }
            // Pausar/reanudar según visibilidad de la pestaña
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    consultarNotificaciones(); // Consultar inmediatamente al volver
                    iniciarPolling();
                } else {
                    detenerPolling();
                }
            });
            // Arrancar al cargar la página
            document.addEventListener('DOMContentLoaded', function () {
                iniciarPolling();
            });
        })();
    </script>

</body>
<!-- END: Body-->

</html>
