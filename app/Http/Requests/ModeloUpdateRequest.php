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
            'modelo' => ['required', 'min:3', 'max:128', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/', Rule::unique('modelos')->ignore($modelo->id)],
            'marca_id' => ['required', 'exists:marcas,id'],
        ];
    }
}
