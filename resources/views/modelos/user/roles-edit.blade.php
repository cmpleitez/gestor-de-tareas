@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                            <div class="col-md-10 align-items-center" style="padding: 0 0 0 0;">
                                <p>ROLES DE {{ strtoupper($user->name) }}</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('user') !!}">
                                    <div class="badge badge-pill btn-secondary-dark">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('user.roles-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <!-- Roles Múltiples -->
                            <div class="form-group">
                                <label class="form-label">Roles Adicionales</label>
                                <div class="selectable-items-container">
                                    @foreach ($roles as $role)
                                        <div class="selectable-item {{ $user->hasRole($role->name) ? 'selected' : '' }}"
                                            onclick="toggleRole('role_{{ $loop->index }}')">
                                            <div class="checkbox-indicator {{ $user->hasRole($role->name) ? 'checked' : '' }}"
                                                id="checkbox_role_{{ $loop->index }}"></div>
                                            <div class="item-body">
                                                <div class="item-info">
                                                    <div class="item-name">{{ $role->name }}</div>
                                                    <div class="item-desc">
                                                        Rol
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="checkbox" name="roles[]" id="role_{{ $loop->index }}"
                                                value="{{ $role->name }}"
                                                {{ $user->hasRole($role->name) ? 'checked' : '' }} style="display: none;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <!-- Rol Principal -->
                        <div class="form-group mb-0" style="min-width: 250px;">
                            <label class="form-label" for="role_id" style="margin-bottom: 0.5rem; font-size: 0.8rem;">Rol
                                Principal</label>
                            <select class="form-control form-control-sm" id="role_id" name="role_id" required>
                                <option value="">Seleccione el rol principal</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $user->mainRole->name == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary-dark">Otorgar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Función para alternar la selección de roles múltiples
        function toggleRole(checkboxId) {
            const checkbox = document.getElementById(checkboxId);
            const selectableItem = checkbox.closest('.selectable-item');
            const checkboxIndicator = selectableItem.querySelector('.checkbox-indicator');

            // Alternar el estado del checkbox
            checkbox.checked = !checkbox.checked;

            // Actualizar la apariencia visual
            if (checkbox.checked) {
                selectableItem.classList.add('selected');
                checkboxIndicator.classList.add('checked');

                // Sincronizar con el rol principal
                const roleName = checkbox.value;
                const roleSelect = document.getElementById('role_id');

                // Buscar la opción que coincida con el nombre del rol
                for (let option of roleSelect.options) {
                    if (option.text === roleName) {
                        roleSelect.value = option.value;
                        break;
                    }
                }
            } else {
                selectableItem.classList.remove('selected');
                checkboxIndicator.classList.remove('checked');

                // Si se deselecciona, verificar si era el rol principal
                const roleName = checkbox.value;
                const roleSelect = document.getElementById('role_id');

                // Si el rol deseleccionado era el principal, limpiar el select
                for (let option of roleSelect.options) {
                    if (option.text === roleName && option.value === roleSelect.value) {
                        roleSelect.value = '';
                        break;
                    }
                }
            }
        }
    </script>
@stop
