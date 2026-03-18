<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarcaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $marca = $this->route('marca');
        return [
            'marca' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('marcas')->ignore($marca->id)],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si la marca ya tiene historial
    {
        $validator->after(function ($validator) {
            $marca          = $this->route('marca');
            $nombreCambio   = $this->input('marca') !== $marca->marca;
            $tieneHistorial = $marca->modelos()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('marca', 'No se puede cambiar el nombre de la marca porque ya tiene modelos asociados.');
            }
        });
    }
}
