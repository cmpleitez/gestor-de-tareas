<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModeloUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $modelo = $this->route('modelo');
        return [
            'modelo' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('modelos')->ignore($modelo->id)],
            'marca_id' => ['required', 'exists:marcas,id'],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si el modelo ya tiene historial
    {
        $validator->after(function ($validator) {
            $modelo         = $this->route('modelo');
            $nombreCambio   = $this->input('modelo') !== $modelo->modelo;
            $tieneHistorial = $modelo->productos()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('modelo', 'No se puede cambiar el nombre del modelo porque ya tiene productos asociados.');
            }
        });
    }
}
