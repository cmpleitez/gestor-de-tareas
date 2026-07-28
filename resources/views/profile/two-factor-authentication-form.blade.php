<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Autenticación de dos factores</h4>
        </div>
        <div class="card-content">
            <div class="card-body">

                <h5>
                    @if ($this->enabled)
                        @if ($showingConfirmation)
                            Termine de habilitar la autenticación de dos factores.
                        @else
                            Ha habilitado la autenticación de dos factores.
                        @endif
                    @else
                        No ha habilitado la autenticación de dos factores.
                    @endif
                </h5>

                <p class="text-muted">
                    Cuando la autenticación de dos factores está habilitada, se le pedirá un código aleatorio y seguro
                    al iniciar sesión. Ese código lo genera una aplicación de autenticación en su teléfono.
                </p>

                @if ($this->enabled)
                    @if ($showingQrCode)
                        <p class="text-bold-600">
                            @if ($showingConfirmation)
                                Escanee el siguiente código QR con la aplicación de autenticación de su teléfono e
                                ingrese el código generado para terminar de habilitarla.
                            @else
                                Escanee el siguiente código QR con la aplicación de autenticación de su teléfono.
                            @endif
                        </p>

                        <div class="d-inline-block bg-white p-1 mb-1 border">
                            {!! $this->user->twoFactorQrCodeSvg() !!}
                        </div>

                        <p class="text-bold-600">
                            Clave de configuración: <span class="text-monospace">{{ decrypt($this->user->two_factor_secret) }}</span>
                        </p>

                        @if ($showingConfirmation)
                            <div class="form-group col-md-4 px-0">
                                <label class="text-bold-600" for="code">Código</label>
                                <input type="text" id="code" class="form-control @error('code') is-invalid @enderror"
                                    inputmode="numeric" autocomplete="one-time-code" autofocus wire:model="code">
                                @error('code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    @endif

                    @if ($showingRecoveryCodes)
                        <p class="text-bold-600">
                            Guarde estos códigos de recuperación en un lugar seguro. Le permiten recuperar el acceso a
                            su cuenta si pierde el dispositivo de autenticación.
                        </p>

                        <div class="bg-light-secondary rounded p-1 mb-1 text-monospace">
                            @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $codigoRecuperacion)
                                <div>{{ $codigoRecuperacion }}</div>
                            @endforeach
                        </div>
                    @endif
                @endif

            </div>
        </div>
        <div class="card-footer">
            @if (!$this->enabled)
                <button type="button" class="btn btn-primary" wire:loading.attr="disabled"
                    wire:click="startConfirmingPassword('enableTwoFactorAuthentication')">
                    Habilitar
                </button>
            @else
                @if ($showingRecoveryCodes)
                    <button type="button" class="btn btn-secondary mr-50" wire:loading.attr="disabled"
                        wire:click="startConfirmingPassword('regenerateRecoveryCodes')">
                        Regenerar códigos de recuperación
                    </button>
                @elseif ($showingConfirmation)
                    <button type="button" class="btn btn-primary mr-50" wire:loading.attr="disabled"
                        wire:click="startConfirmingPassword('confirmTwoFactorAuthentication')">
                        Confirmar
                    </button>
                @else
                    <button type="button" class="btn btn-secondary mr-50" wire:loading.attr="disabled"
                        wire:click="startConfirmingPassword('showRecoveryCodes')">
                        Mostrar códigos de recuperación
                    </button>
                @endif

                <button type="button" class="btn btn-danger" wire:loading.attr="disabled"
                    wire:click="startConfirmingPassword('disableTwoFactorAuthentication')">
                    {{ $showingConfirmation ? 'Cancelar' : 'Deshabilitar' }}
                </button>
            @endif
        </div>
    </div>

    {{-- Confirmación de contraseña: modal de Bootstrap gobernado por el estado del componente, sin la API de JS --}}
    @if ($confirmingPassword)
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar clave</h5>
                        <button type="button" class="close" wire:click="stopConfirmingPassword">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Por su seguridad, confirme su clave para continuar.</p>
                        <input type="password" class="form-control @error('confirmable_password') is-invalid @enderror"
                            placeholder="Clave" autocomplete="current-password" autofocus
                            wire:model="confirmablePassword" wire:keydown.enter="confirmPassword">
                        @error('confirmable_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:loading.attr="disabled"
                            wire:click="stopConfirmingPassword">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:click="confirmPassword">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @script
        <script>
            // El id que viaja en el evento es el nombre del método a ejecutar una vez validada la clave
            Livewire.on('password-confirmed', ({ id }) => $wire.call(id));
        </script>
    @endscript
</div>
