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
                                <p>TAREAS DE {{ strtoupper($solicitud->solicitud) }}</p>
                            </div>
                            <div class="col-md-2 d-flex justify-content-end" style="padding: 0.1rem;">
                                <a href="{!! route('solicitud') !!}">
                                    <div class="badge badge-pill badge-primary">
                                        <i class="bx bx-arrow-back font-medium-3"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('solicitud.actualizar-tareas', ['solicitud' => $solicitud->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-content">
                        <div class="card-body">
                            <div class="tareas-grid">
                                @foreach ($tareas as $tarea)
                                    <div class="tarea-card selectable-item" onclick="toggleCheckbox('{{ $loop->index }}')">
                                        <div class="tarea-header">
                                            <div class="tarea-icon bg-secondary-dark">
                                                <i class="bx bx-task"></i>
                                            </div>
                                            <div class="tarea-checkbox">
                                                @if ($solicitud->tareas->contains($tarea->id))
                                                    <input type="checkbox" name="tareas[]" class="form-check-input tarea-checkbox-input" 
                                                    id="{{ $loop->index }}" value="{{ $tarea->id }}" checked>
                                                @else
                                                    <input type="checkbox" name="tareas[]" class="form-check-input tarea-checkbox-input" 
                                                    id="{{ $loop->index }}" value="{{ $tarea->id }}">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="tarea-content d-flex justify-content-center align-items-center">
                                            {{ $tarea->tarea }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function toggleCheckbox(id) {
            const checkbox = document.getElementById(id);
            const card = checkbox.closest('.tarea-card');
            
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                
                // Actualizar estado visual de la tarjeta
                if (checkbox.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.tarea-checkbox-input');
            
            // Inicializar estado visual de las tarjetas
            checkboxes.forEach(function(checkbox) {
                const card = checkbox.closest('.tarea-card');
                if (checkbox.checked) {
                    card.classList.add('selected');
                }
                
                // Prevenir propagación del click en el checkbox
                checkbox.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>
@stop
