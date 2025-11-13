<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Gestor de tareas">
    <meta name="keywords" content="">
    <meta name="author" content="Carlos Pleitez - cpleitez.2024@gmail.com">
    <title>{{ config('app.version') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('app-assets/images/logo/logo.svg') }}">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700"
        rel="stylesheet">

    <!-- BEGIN: All CSS-->
    <link href="{{ asset('app-assets/vendors/css/vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}" rel="stylesheet">
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
    <!-- END: All CSS-->

    <style>
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
            color: rgb(255, 58, 84) !important;
            border: 1px solid rgb(255, 58, 84) !important;
            font-size: 1.1rem !important;
            border-radius: 0.2rem !important;
            padding: 0.3rem 0.3rem 0 0.3rem;
            margin-right: 0.1rem;
        }

        /* Fix para mantener el footer estático */
        .footer {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1000 !important;
            margin: 0 !important;
        }

        body {
            padding-bottom: 60px !important;
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

<body class="vertical-layout boxicon-layout no-card-shadow footer-static">

    <!-- LOGO -->
    <div class="row justify-content-center mb-3 mt-3">
        <div class="col-auto">
            <img src="{{ asset('app-assets/images/logo/logo.svg') }}" alt="Logo" class="auth-logo">
        </div>
    </div>
    <!-- FORMULARIO -->
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header pt-1"> {{-- Cabecera --}}
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0rem;">
                            <div class="col-md-10 align-items-center" style="padding-left: 0.5rem;">
                                <p>REGISTRO DE NUEVO USUARIO</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('user') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('register') }}" method="POST"
                    enctype="multipart/form-data" novalidate> {{-- Contenido --}}
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12"> {{-- Nombre --}}
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <div class="controls">
                                            <input type="text" name="name" id="name"
                                                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="Este campo es obligatorio"
                                                data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$"
                                                data-validation-containsnumber-message="Solo se permiten letras, sin espacios al inicio/final ni dobles espacios"
                                                data-validation-minlength-message="El nombre debe tener al menos 3 caracteres"
                                                data-clear="true" minlength="3" placeholder="Nombre del nuevo usuario"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="col-sm-12 badge bg-danger text-wrap"
                                                    style="margin-top: 0.2rem;">
                                                    {{ $errors->first('name') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12"> {{-- DUI --}}
                                    <div class="form-group">
                                        <label>DUI</label>
                                        <div class="controls">
                                            <input type="text" name="dui" id="dui" maxlength="9"
                                                minlength="9"
                                                class="form-control {{ $errors->has('dui') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="El DUI es requerido."
                                                data-validation-regex-regex="^\d{9}$"
                                                data-validation-regex-message="El D.U.I. ingresado no es válido."
                                                data-clear="true"
                                                placeholder="Ingrese los 9 dígitos del DUI sin guión"
                                                value="{{ old('dui') }}" required>
                                            @error('dui')
                                                <div class="col-sm-12 badge bg-danger text-wrap"
                                                    style="margin-top: 0.2rem;">
                                                    {{ $errors->first('dui') }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12"> {{-- Fotografia --}}
                                    <div class="form-group">
                                        <label>Fotografia del Operador <small class="text-muted">(Máximo 5 MB, solo
                                                JPEG/PNG)</small></label>
                                        <input type="file" name="profile_photo_path" class="form-control"
                                            style="padding-bottom: 35px;" accept="image/jpeg,image/jpg,image/png"
                                            onchange="validateFileSize(this, 5)">
                                        <small class="form-text text-muted">Formatos permitidos: JPEG, JPG, PNG. Tamaño
                                            máximo: 5 MB</small>
                                    </div>
                                    @error('profile_photo_path')
                                        <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                            {{ $errors->first('profile_photo_path') }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-sm-12"> {{-- Correo --}}
                                    <div class="form-group">
                                        <label>Correo electrónico</label>
                                        <div class="controls">
                                            <input type="email" name="email" id="email"
                                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="El email es requerido."
                                                data-validation-email-message="Debe ser un correo electrónico válido"
                                                data-clear="true" placeholder="correo@ejemplo.com"
                                                value="{{ old('email') }}" required>
                                        </div>
                                        @error('email')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('email') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6"> {{-- Clave --}}
                                    <div class="form-group">
                                        <label>Clave</label>
                                        <div class="controls">
                                            <input type="password" name="password" id="password"
                                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                                data-validation-required-message="La contraseña es requerida."
                                                data-validation-minlength-message="La contraseña debe tener al menos 6 caracteres."
                                                data-validation-maxlength-message="La contraseña debe tener máximo 16 caracteres."
                                                minlength="6" maxlength="16"
                                                placeholder="Una contraseña de 6 a 16 caracteres"
                                                value="{{ old('password') }}" required>
                                        </div>
                                        @error('password')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('password') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6"> {{-- Confirmar clave --}}
                                    <div class="form-group">
                                        <label>Confirmar clave</label>
                                        <div class="controls">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                                data-validation-match-match="password"
                                                data-validation-required-message="Este campo es obligatorio"
                                                data-validation-match-message="Las contraseñas no coinciden."
                                                minlength="6" maxlength="16" placeholder="Clave de confirmación"
                                                value="{{ old('password_confirmation') }}" required>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('password_confirmation') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end"> {{-- Guardar --}}
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <span id="btn-text">Guardar</span>
                            <span id="btn-spinner" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
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

    <!-- BEGIN: All JavaScript-->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js') }}"></script>
    <script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/input-clear.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/configs/vertical-menu-light.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/footer.js') }}"></script>
    <!-- END: All JavaScript-->

    <!-- BEGIN: Page JS-->
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            /*
                        window.validateFileSize = function(input, maxSizeMB) { // Función para validar tamaño de archivo
                            const file = input.files[0];
                            if (file) {
                                const fileSizeMB = file.size / (1024 * 1024);
                                if (fileSizeMB > maxSizeMB) {
                                    alert('El archivo es demasiado grande. El tamaño máximo permitido es ' + maxSizeMB +
                                        'MB.');
                                    input.value = '';
                                    return false;
                                }
                                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']; // Validar tipo de archivo
                                if (!allowedTypes.includes(file.type)) {
                                    alert('Solo se permiten archivos JPEG, JPG o PNG.');
                                    input.value = '';
                                    return false;
                                }
                            }
                            return true;
                        };
            */

            $('form').on('submit', function() { // Loading spinner en el botón de envío
                $('#submit-btn').prop('disabled', true);
                $('#btn-text').hide();
                $('#btn-spinner').show();
                $('body').append(` 
                    <div id="loading-overlay" style="
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        z-index: 9999;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    ">
                        <div style="
                            background: white;
                            padding: 30px;
                            border-radius: 10px;
                            text-align: center;
                            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                        ">
                            <div style="
                                font-size: 24px;
                                color: #007bff;
                                margin-bottom: 15px;
                            ">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <h4 style="margin: 0; color: #333;">Enviando correo de confirmación...</h4>
                            <p style="margin: 10px 0 0 0; color: #666;">Por favor espere mientras finaliza el proceso</p>
                        </div>
                    </div>
                `);
            });
        });
    </script>
    <!-- END: Page JS-->
</body>
<!-- END: Body-->

</html>
