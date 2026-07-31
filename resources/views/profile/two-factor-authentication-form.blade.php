<div class = "col-md-12">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-1">
            <div class="d-flex align-items-center">
                <div class="avatar bg-light-primary p-50 mr-1">
                    <i class="bx bx-shield-quarter font-medium-5 text-primary"></i>
                </div>
                <div>
                    <h4 class="card-title mb-0">Autenticación de Dos Factores (2FA)</h4>
                    <small class="text-muted">Protección adicional para su cuenta de usuario</small>
                </div>
            </div>
            <div>
                @if ($this->enabled)
                    @if ($showingConfirmation)
                        <span class="badge badge-pill badge-light-warning font-weight-bold">
                            <i class="bx bx-time-five mr-25"></i> Pendiente de confirmación
                        </span>
                    @else
                        <span class="badge badge-pill badge-light-success font-weight-bold">
                            <i class="bx bx-check-shield mr-25"></i> Habilitado
                        </span>
                    @endif
                @else
                    <span class="badge badge-pill badge-light-secondary font-weight-bold">
                        <i class="bx bx-lock-alt mr-25"></i> Deshabilitado
                    </span>
                @endif
            </div>
        </div>

        <div class="card-content">
            <div class="card-body">
                <div class="alert alert-light-info d-flex align-items-center mb-2" role="alert">
                    <i class="bx bx-info-circle font-medium-3 mr-1 text-info"></i>
                    <span class="font-small-3">
                        Cuando la autenticación de dos factores está habilitada, se le pedirá un código aleatorio y seguro
                        al iniciar sesión, generado por la aplicación de autenticación en su teléfono.
                    </span>
                </div>

                <div class="mb-2">
                    <h5 class="font-weight-600 mb-50">
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
                </div>

                @if ($this->enabled)
                    @if ($showingQrCode)
                        <div class="border rounded p-2 mb-2 bg-light-primary border-primary">
                            <p class="font-weight-bold text-primary mb-1">
                                <i class="bx bx-mobile-alt mr-50"></i>
                                @if ($showingConfirmation)
                                    Escanee el siguiente código QR con la aplicación de autenticación de su teléfono e
                                    ingrese el código generado para terminar de habilitarla.
                                @else
                                    Escanee el siguiente código QR con la aplicación de autenticación de su teléfono.
                                @endif
                            </p>

                            <div class="text-center py-1">
                                <div class="d-inline-block bg-white p-2 rounded shadow-sm border">
                                    {!! $this->user->twoFactorQrCodeSvg() !!}
                                </div>
                            </div>

                            <div class="text-center mt-1">
                                <p class="font-small-3 text-muted mb-25">Clave de configuración manual:</p>
                                <span class="badge badge-light-primary text-monospace p-75 font-medium-1">
                                    {{ decrypt($this->user->two_factor_secret) }}
                                </span>
                            </div>

                            @if ($showingConfirmation)
                                <div class="row justify-content-center mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold text-dark" for="code">
                                                <i class="bx bx-key mr-25"></i> Código de verificación
                                            </label>
                                            <input type="text" id="code" class="form-control text-center font-medium-2 @error('code') is-invalid @enderror"
                                                placeholder="000000" inputmode="numeric" autocomplete="one-time-code" autofocus wire:model="code">
                                            @error('code')
                                                <div class="invalid-feedback d-block text-center">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($showingRecoveryCodes)
                        <div class="border rounded p-2 mb-2 bg-light">
                            <p class="font-weight-bold text-dark mb-1">
                                <i class="bx bx-receipt mr-50"></i>
                                Guarde estos códigos de recuperación en un lugar seguro. Le permiten recuperar el acceso a
                                su cuenta si pierde el dispositivo de autenticación.
                            </p>

                            <div class="bg-white rounded p-1 mb-1 border font-monospace">
                                <div class="row">
                                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $codigoRecuperacion)
                                        <div class="col-6 py-25 px-1 text-center font-weight-bold text-secondary">
                                            <code>{{ $codigoRecuperacion }}</code>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="card-footer bg-transparent border-top d-flex align-items-center justify-content-end py-1">
            @if (!$this->enabled)
                <button type="button" class="btn btn-primary shadow-sm" wire:loading.attr="disabled"
                    wire:click="startConfirmingPassword('enableTwoFactorAuthentication')">
                    <i class="bx bx-shield-plus mr-50"></i> Habilitar
                </button>
            @else
                @if ($showingRecoveryCodes)
                    <button type="button" class="btn btn-outline-secondary mr-1" wire:loading.attr="disabled"
                        wire:click="startConfirmingPassword('regenerateRecoveryCodes')">
                        <i class="bx bx-refresh mr-50"></i> Regenerar códigos
                    </button>
                @elseif ($showingConfirmation)
                    <button type="button" class="btn btn-success mr-1 shadow-sm" wire:loading.attr="disabled"
                        wire:click="startConfirmingPassword('confirmTwoFactorAuthentication')">
                        <i class="bx bx-check-circle mr-50"></i> Confirmar
                    </button>
                @else
                    <button type="button" class="btn btn-outline-info mr-1" wire:loading.attr="disabled"
                        wire:click="startConfirmingPassword('showRecoveryCodes')">
                        <i class="bx bx-show mr-50"></i> Mostrar códigos de recuperación
                    </button>
                @endif

                <button type="button" class="btn btn-outline-danger" wire:loading.attr="disabled"
                    wire:click="startConfirmingPassword('disableTwoFactorAuthentication')">
                    <i class="bx bx-power-off mr-50"></i> {{ $showingConfirmation ? 'Cancelar' : 'Deshabilitar' }}
                </button>
            @endif
        </div>
    </div>

    {{-- Confirmación de clave modal --}} {{-- Modal gobernado por el componente Livewire --}}
    @if ($confirmingPassword)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-light-primary border-bottom">
                        <h5 class="modal-title font-weight-bold text-primary">
                            <i class="bx bx-lock-alt mr-50"></i> Confirmar contraseña
                        </h5>
                        <button type="button" class="close text-primary" wire:click="stopConfirmingPassword">&times;</button>
                    </div>
                    <div class="modal-body p-2">
                        <p class="text-muted font-small-3">Por su seguridad, confirme su contraseña para continuar.</p>
                        <div class="form-group mb-0">
                            <input type="password" class="form-control @error('confirmable_password') is-invalid @enderror"
                                placeholder="Ingrese su contraseña" autocomplete="current-password" autofocus
                                wire:model="confirmablePassword" wire:keydown.enter="confirmPassword">
                            @error('confirmable_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-light-secondary" wire:loading.attr="disabled"
                            wire:click="stopConfirmingPassword">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:click="confirmPassword">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @script
        <script>
            // El id que viaja en el evento es el nombre del método a ejecutar una vez validada la clave
            Livewire.on('password-confirmed', ({ id }) => $wire.call(id));
        </script>
    @endscript
</div>

