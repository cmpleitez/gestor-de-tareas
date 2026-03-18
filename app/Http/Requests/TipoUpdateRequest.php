<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo = $this->route('tipo');
        return [
            'tipo' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('tipos')->ignore($tipo->id)],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si el tipo ya tiene historial
    {
        $validator->after(function ($validator) {
            $tipo           = $this->route('tipo');
            $nombreCambio   = $this->input('tipo') !== $tipo->tipo;
            $tieneHistorial = $tipo->productos()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('tipo', 'No se puede cambiar el nombre del tipo porque ya tiene productos asociados.');
            }
        });
    }
}
