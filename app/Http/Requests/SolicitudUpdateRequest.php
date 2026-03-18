<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitudUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $solicitud = $this->route('solicitud');
        return [
            'solicitud' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('solicitudes')->ignore($solicitud->id)],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si la solicitud ya tiene historial
    {
        $validator->after(function ($validator) {
            $solicitud      = $this->route('solicitud');
            $nombreCambio   = $this->input('solicitud') !== $solicitud->solicitud;
            $tieneHistorial = $solicitud->tareas()->exists() || $solicitud->recepciones()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('solicitud', 'No se puede cambiar el nombre de la solicitud porque ya tiene tareas o recepciones asociadas.');
            }
        });
    }
}
