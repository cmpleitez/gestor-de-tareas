<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_id' => 'required|exists:tipos,id',
            'modelo_id' => 'required|exists:modelos,id',
            'producto' => 'required|unique:productos|min:3|max:128|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/',
            'precio' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }
}
