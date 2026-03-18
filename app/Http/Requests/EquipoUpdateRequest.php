<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipo = $this->route('equipo');
        return [
            'equipo' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('equipos')->ignore($equipo->id)],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si el equipo ya tiene historial
    {
        $validator->after(function ($validator) {
            $equipo        = $this->route('equipo');
            $nombreCambio  = $this->input('equipo') !== $equipo->equipo;
            $tieneHistorial = $equipo->users()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('equipo', 'No se puede cambiar el nombre del equipo porque ya tiene usuarios asignados y un posible uso del mismo en el código fuente de la aplicación.');
            }
        });
    }
}

