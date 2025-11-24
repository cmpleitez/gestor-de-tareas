<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ auth()->user()->mainRole->name }} - ALFA.{{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('app-assets/css/bootstrap-5.0.0-beta1.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/templatemo-zay.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/custom-zay.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/fontawesome-zay.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/colors.css') }}">
    <link href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}" rel="stylesheet">

    @stack('css')
</head>

<body>

    <!-- Header Section -->
    <header class="bg-primary-dark text-white py-3">
        <div class="container">
            <div class="row align-items-center flex-nowrap">
                <div class="col-auto d-flex justify-content-start">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col d-flex justify-content-center">
                    <h1 class="h3 mb-0 text-truncate">CATÁLOGO</h1>
                </div>
                <div class="col-auto d-flex justify-content-end">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-cart-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    Texto a la izquierda del pie de página
                </div>
                <div class="col-md-6 text-end">
                    Texto a la derecha del pie de página
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Files -->
    <script src="{{ asset('app-assets/js/jquery-1.11.0-zay.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/jquery-migrate-1.2.1-zay.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/bootstrap-bundle-5-zay.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/templatemo-zay.js') }}"></script>
    <script src="{{ asset('app-assets/js/custom-zay.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>

    <script>
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true
                , "debug": false
                , "newestOnTop": false
                , "progressBar": true
                , "positionClass": "toast-top-right"
                , "preventDuplicates": false
                , "onclick": null
                , "showDuration": "300"
                , "hideDuration": "1000"
                , "timeOut": "5000"
                , "extendedTimeOut": "1000"
                , "showEasing": "swing"
                , "hideEasing": "linear"
                , "showMethod": "fadeIn"
                , "hideMethod": "fadeOut"
            };
        }
        $(document).ready(function() {
            @if(session('success'))
            toastr.success({
                !!json_encode(session('success')) !!
            });
            @endif
            @if(session('error'))
            toastr.error({
                !!json_encode(session('error')) !!
            }, '', {
                timeOut: 0
                , extendedTimeOut: 0
                , closeButton: true
            });
            @endif
            @if(session('warning'))
            toastr.warning({
                !!json_encode(session('warning')) !!
            });
            @endif
            @if(session('info'))
            toastr.info({
                !!json_encode(session('info')) !!
            });
            @endif
            @if(session('danger'))
            toastr.error({
                !!json_encode(session('danger')) !!
            });
            @endif
        });

    </script>
    @stack('scripts')
</body>

</html>
