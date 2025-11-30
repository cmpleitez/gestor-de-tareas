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
            'modelo' => 'required|unique:modelos|min:3|max:255|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/',
            'marca_id' => 'required|exists:marcas,id',
        ];
    }
}
