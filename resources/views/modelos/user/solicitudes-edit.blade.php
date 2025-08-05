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
                                <p>HABILIDADES DE {{ strtoupper($user->name) }}</p>
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
                <form action="{{ route('user.solicitudes-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="selectable-items-container">
                                @foreach ($solicitudes as $solicitud)
                                    <div class="selectable-item {{ $user->solicitudes->contains($solicitud->id) ? 'selected' : '' }}" 
                                         onclick="toggleSolicitud('solicitud_{{ $solicitud->id }}')">
                                        <div class="checkbox-indicator {{ $user->solicitudes->contains($solicitud->id) ? 'checked' : '' }}" 
                                             id="checkbox_solicitud_{{ $solicitud->id }}"></div>
                                        <div class="item-body">
                                            <div class="item-info">
                                                <div class="item-name">{{ $solicitud->solicitud }}</div>
                                                <div class="item-desc">Solicitud</div>
                                            </div>
                                        </div>
                                        <input type="checkbox" name="solicitudes[]" 
                                               id="solicitud_{{ $solicitud->id }}" 
                                               value="{{ $solicitud->id }}" 
                                               {{ $user->solicitudes->contains($solicitud->id) ? 'checked' : '' }}
                                               style="display: none;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Función para alternar la selección de solicitudes
    function toggleSolicitud(checkboxId) {
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
