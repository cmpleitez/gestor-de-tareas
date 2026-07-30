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
                                <p>ROL DE {{ strtoupper($user->name) }}</p>
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
                             <!-- Roles Selección Única -->
                             <div class="form-group">
                                 <div class="selectable-items-container">
                                     @foreach ($roles as $role)
                                         <div class="selectable-item {{ $user->hasRole($role->name) ? 'selected' : '' }}"
                                             onclick="selectRole('role_{{ $loop->index }}')">
                                             <div class="radio-indicator {{ $user->hasRole($role->name) ? 'checked' : '' }}"
                                                 id="checkbox_role_{{ $loop->index }}"></div>
                                             <div class="item-body">
                                                 <div class="item-info">
                                                     <div class="item-name">{{ $role->name }}</div>
                                                     <div class="item-desc">
                                                         Rol
                                                     </div>
                                                 </div>
                                             </div>
                                             <input type="radio" name="roles[]" id="role_{{ $loop->index }}"
                                                 value="{{ $role->name }}"
                                                 {{ $user->hasRole($role->name) ? 'checked' : '' }} style="display: none;">
                                         </div>
                                     @endforeach
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="card-footer d-flex justify-content-end align-items-center">
                         <button type="submit" class="btn btn-secondary-dark">Otorgar</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
@stop

@section('js')
    <script>
        // Quita la selección de un rol
        function desmarcarRol(radio) {
            if (!radio.checked) return;
            radio.checked = false;
            const item = radio.closest('.selectable-item');
            item.classList.remove('selected');
            item.querySelector('.radio-indicator').classList.remove('checked');
        }

        // Selección única de rol
        function selectRole(radioId) {
            const radio = document.getElementById(radioId);
            const selectableItem = radio.closest('.selectable-item');
            const radioIndicator = selectableItem.querySelector('.radio-indicator');

            // Desmarcar los demás roles
            document.querySelectorAll('input[name="roles[]"]').forEach(function(otro) {
                if (otro !== radio) {
                    desmarcarRol(otro);
                }
            });

            // Marcar el seleccionado
            radio.checked = true;
            selectableItem.classList.add('selected');
            radioIndicator.classList.add('checked');
        }
    </script>
@stop
