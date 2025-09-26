<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipo'     => 'required|unique:equipos|min:3|max:128|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/',
            'oficina_id' => 'required|exists:oficinas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'oficina_id.exists' => 'La oficina seleccionada no existe.',
        ];
    }
}
