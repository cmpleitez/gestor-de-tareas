<x-guest-layout>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <!-- CABECERA -->
            <div class="card-header pt-1">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between" style="padding: 0rem;">
                        <div class="col-md-10 align-items-center" style="padding-left: 0.5rem;">
                            <p>NUEVO USUARIO </p>
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
            <!-- FORMULARIO -->
            <form class="form-horizontal" action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data"
                novalidate>
                @csrf
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            {{-- NOMBRE --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <div class="controls">
                                        <input type="text" name="name"
                                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                            data-validation-required-message="El nombre es requerido"
                                            data-validation-containsnumber-regex="^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$"
                                            data-validation-containsnumber-message="El nombre debe contener solo letras (incluyendo tildes), no se permiten dobles espacios entre palabras, ni espacios al principio o final de las palabras."
                                            placeholder="Nombre del nuevo usuario" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('name') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- DUI --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>DUI</label>
                                    <div class="controls">
                                        <input type="text" name="dui" maxlength="10" minlength="10"
                                            class="form-control {{ $errors->has('dui') ? 'is-invalid' : '' }}"
                                            data-validation-required-message="El DUI es requerido"
                                            placeholder="Formato de DUI válido (Con el guión): 00000000-0" value="{{ old('dui') }}" required>
                                        @error('dui')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('dui') }}
                                            </div>
                                        @enderror
                                        @error('dui_clean')
                                            <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                                {{ $errors->first('dui_clean') }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- FOTOGRAFIA --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Fotografia del Operador</label>
                                    <input type="file" name="profile_photo_path" class="form-control" style="padding-bottom: 35px;">
                                </div>
                                @error('profile_photo_path')
                                    <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                        {{ $errors->first('profile_photo_path') }}
                                    </div>
                                @enderror
                            </div>
                            {{-- OFICINA --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="oficina_id">Oficina</label>
                                    <select class="select2 form-control" id="oficina_id" name="oficina_id" 
                                    data-validation-required-message="La oficina es requerida" required>
                                        @foreach ($oficinas as $oficina)
                                            <option value="{{ $oficina->id }}" {{ old('oficina_id') == $oficina->id ? 'selected' : '' }}>
                                                {{ $oficina->oficina }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('oficina_id')
                                        <div class="col-sm-12 badge bg-danger text-wrap" style="margin-top: 0.2rem;">
                                            {{ $errors->first('oficina_id') }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            {{-- CORREO --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Correo electrónico</label>
                                    <div class="controls">
                                        <input type="email" name="email" class="form-control"
                                            placeholder="correo@ejemplo.com"
                                            data-validation-required-message="El correo electrónico es requerido"
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
                            {{-- CLAVE --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Clave</label>
                                    <div class="controls">
                                        <input type="password" name="password" class="form-control"
                                            data-validation-required-message="La clave debe ser una cadena de 6 a 16 caracteres"
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
                            {{-- CONFIRMAR CLAVE --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Confirmar clave</label>
                                    <div class="controls">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            data-validation-match-match="password"
                                            data-validation-required-message="Se requiere una clave de confirmación"
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
                {{-- GUARDAR --}}
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <x-authentication-card>
        
        <x-slot name="logo" class="d-flex justify-content-center">
            <img src="/app-assets/images/logo/logo.png" alt="Logo" style="height: 100px; width: auto; object-fit: contain;">
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <x-label for="name" value="Nombre" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Correo electrónico') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Clave') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmación de clave') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="image" value="{{ __('Imagen de perfil') }}" />
                <x-input id="image" class="block mt-1 w-full" type="file" name="image" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    ¿Ya estoy registrado?
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
