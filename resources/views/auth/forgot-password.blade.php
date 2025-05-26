<x-guest-layout>
    <x-authentication-card>
        
        <x-slot name="logo" class="d-flex justify-content-center">
            <img src="/app-assets/images/logo/logo.png" alt="Logo" style="height: 100px; width: auto; object-fit: contain;">
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            ¿Olvidaste tu clave? No hay problema. Envíanos tu correo electrónico y te devolveremos un enlace que te permitirá elegir una nueva.
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="Correo electrónico" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    Enviar mi correo electrónico
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
