<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ auth()->user()->mainRole->name }} - ALFA.{{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link rel="stylesheet" href="{{ asset('app-assets/css/bootstrap-5.0.0-beta1.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/templatemo-zay.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/custom-zay.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/fontawesome-zay.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/extensions/toastr.css') }}">

    <style>
        .button_delete {
            font-size: 1.1rem !important;
            border-radius: 0.2rem !important;
            padding: 0.01em 0.3em 0.01em 0.3em;
            margin-right: 0.1rem;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        main .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        main .container>* {
            flex-shrink: 0;
        }

        main .container>.d-flex {
            flex: 1;
            min-height: 0;
        }

        .badge-carrito-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #ffc107;
            color: #000;
            border-radius: 50%;
            width: 17px;
            height: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            line-height: 1;
        }

        .badge-carrito-count.d-none {
            display: none !important;
        }

        @media (max-width: 768px) {
            #modalPreferencias {
                padding-right: 0 !important;
            }

            #modalPreferencias .modal-dialog {
                margin: 0;
                max-width: 100%;
                width: 100%;
                height: 100%;
                min-height: 100%;
            }

            #modalPreferencias .modal-content {
                border-radius: 0;
                min-height: 100%;
                height: 100%;
            }

            #modalPreferencias .modal-body {
                overflow-y: auto;
            }
        }

    </style>
    @stack('css')
</head>

<body>
    <!-- Header Section -->
    <header class="bg-secondary-dark text-white py-3">
        <div class="container">
            @yield('header')
        </div>
    </header>
    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>
    <!-- Footer Section -->
    <footer class="bg-secondary-dark text-white py-4 mt-auto">
        <div class="container">
            @yield('footer')
        </div>
    </footer>
    <!-- JavaScript Files -->
    <script src="{{ asset('app-assets/js/jquery-1.11.0-zay.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/jquery-migrate-1.2.1-zay.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/bootstrap-bundle-5-zay.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/templatemo-zay.js') }}"></script>
    <script src="{{ asset('app-assets/js/custom-zay.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/helpers.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script>
        if (typeof toastr !== 'undefined') { // Configuración global de toastr para todo el proyecto
            toastr.options = {
                "closeButton": true
                , "debug": false
                , "newestOnTop": false
                , "progressBar": true
                , "positionClass": "toast-bottom-right"
                , "preventDuplicates": false
                , "onclick": null
                , "showDuration": "300"
                , "hideDuration": "1000"
                , "timeOut": "5000"
                , "extendedTimeOut": "30000"
                , "showEasing": "swing"
                , "hideEasing": "linear"
                , "showMethod": "fadeIn"
                , "hideMethod": "fadeOut"
            };
        }
        $(document).ready(function() {
            @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
            @endif
            @if(Session::has('error'))
                toastr.error("{{ Session::get('error') }}", '', {
                    timeOut: 0
                    , extendedTimeOut: 0
                    , closeButton: true
                });
            @endif
            @if(Session::has('warning'))
                toastr.warning("{{ Session::get('warning') }}");
            @endif
            @if(Session::has('info'))
                toastr.info("{{ Session::get('info') }}");
            @endif
            @if(Session::has('danger'))
                toastr.error("{{ Session::get('danger') }}");
            @endif
            $.ajax({
                url: '{{ route("tienda.cantidad") }}'
                , type: 'GET'
                , dataType: 'json'
                , success: function(response) {
                    var cantidad = response.cantidad || 0;
                    var badge = $('#badge-carrito');

                    if (cantidad > 0) {
                        var texto = cantidad > 9 ? '+9' : cantidad.toString();
                        badge.text(texto).removeClass('d-none');
                    } else {
                        badge.addClass('d-none');
                    }
                }
                , error: function(xhr, status, error) {
                    console.error('Error al cargar cantidad del carrito:', error);
                }
            });
        });

    </script>
    @stack('scripts')
</body>

</html>
