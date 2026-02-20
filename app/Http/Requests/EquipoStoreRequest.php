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
            'equipo'     => 'required|unique:equipos|min:3|max:255|regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/',
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
