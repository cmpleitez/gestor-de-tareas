@extends('dashboard')

@section('css')
@stop

@section('contenedor')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-between" style="padding: 0;">
                            <div class="col-md-10 align-items-center" style="padding: 0 0 0 0;">
                                <p>EQUIPOS DE {{ strtoupper($user->name) }}</p>
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
                <form action="{{ route('user.equipos-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="selectable-items-container">
                                @foreach ($equipos as $equipo)
                                    <div class="selectable-item {{ $user->equipos->contains($equipo->id) ? 'selected' : '' }}" 
                                         onclick="toggleEquipo('equipo_{{ $equipo->id }}')">
                                        <div class="checkbox-indicator {{ $user->equipos->contains($equipo->id) ? 'checked' : '' }}" 
                                             id="checkbox_equipo_{{ $equipo->id }}"></div>
                                        <div class="item-body">
                                            <div class="item-info">
                                                <div class="item-name">{{ $equipo->equipo }}</div>
                                                <div class="item-desc">Equipo</div>
                                            </div>
                                        </div>
                                        <input type="checkbox" name="equipos[]" 
                                               id="equipo_{{ $equipo->id }}" 
                                               value="{{ $equipo->id }}" 
                                               {{ $user->equipos->contains($equipo->id) ? 'checked' : '' }}
                                               style="display: none;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-secondary-dark">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Función para alternar la selección de equipos
    function toggleEquipo(checkboxId) {
        const checkbox = document.getElementById(checkboxId);
        const selectableItem = checkbox.closest('.selectable-item');
        const checkboxIndicator = selectableItem.querySelector('.checkbox-indicator');
        
        // Alternar el estado del checkbox
        checkbox.checked = !checkbox.checked;
        
        // Actualizar la apariencia visual
        if (checkbox.checked) {
            selectableItem.classList.add('selected');
            checkboxIndicator.classList.add('checked');
        } else {
            selectableItem.classList.remove('selected');
            checkboxIndicator.classList.remove('checked');
        }
    }
</script>
@stop
