<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ModeloStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modelo' => 'required|unique:modelos|min:3|max:255|regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/',
            'marca_id' => 'required|exists:marcas,id',
        ];
    }
}
