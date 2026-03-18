<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TareaUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tarea = $this->route('tarea');
        return [
            'tarea' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('tareas')->ignore($tarea->id)],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si la tarea ya tiene historial
    {
        $validator->after(function ($validator) {
            $tarea          = $this->route('tarea');
            $nombreCambio   = $this->input('tarea') !== $tarea->tarea;
            $tieneHistorial = $tarea->usuarios()->exists() || $tarea->solicitudes()->exists() || $tarea->actividades()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('tarea', 'No se puede cambiar el nombre de la tarea porque ya tiene usuarios, solicitudes o actividades asociadas.');
            }
        });
    }
}
