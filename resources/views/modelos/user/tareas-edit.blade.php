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
                                    <div class="badge badge-pill btn-secondary-dark">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('user.tareas-update', ['user' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="card-content">
                        <div class="card-body">
                            <div class="selectable-items-container">
                                @foreach ($tareas as $tarea)
                                    <div class="selectable-item {{ $user->tareas->contains($tarea->id) ? 'selected' : '' }}" 
                                         onclick="toggletarea('tarea_{{ $tarea->id }}')">
                                        <div class="checkbox-indicator {{ $user->tareas->contains($tarea->id) ? 'checked' : '' }}" 
                                             id="checkbox_tarea_{{ $tarea->id }}"></div>
                                        <div class="item-body">
                                            <div class="item-info">
                                                <div class="item-name">{{ $tarea->tarea }}</div>
                                                <div class="item-desc">tarea</div>
                                            </div>
                                        </div>
                                        <input type="checkbox" name="tareas[]" 
                                               id="tarea_{{ $tarea->id }}" 
                                               value="{{ $tarea->id }}" 
                                               {{ $user->tareas->contains($tarea->id) ? 'checked' : '' }}
                                               style="display: none;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-secondary-dark">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Función para alternar la selección de tareas
    function toggletarea(checkboxId) {
        const checkbox = document.getElementById(checkboxId);
        const selectableItem = checkbox.closest('.selectable-item');
        const checkboxIndicator = selectableItem.querySelector('.checkbox-indicator');
        checkbox.checked = !checkbox.checked;
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
