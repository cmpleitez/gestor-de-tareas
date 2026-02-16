<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('precio')) {
            $precio = $this->input('precio');
            $precio = preg_replace('/[^0-9.]/', '', $precio);
            $this->merge([
                'precio' => $precio !== '' ? (float) $precio : null
            ]);
        }
        if ($this->has('codigo')) {
            $this->merge([
                'codigo' => mb_strtoupper($this->input('codigo'))
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'tipo_id' => 'required|exists:tipos,id',
            'modelo_id' => 'required|exists:modelos,id',
            'producto' => 'required|unique:productos|min:3|max:255|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/',
            'codigo' => 'nullable|unique:productos|min:3|max:255|regex:/^(?! )[A-Z0-9ÁÉÍÓÚÑ]+( [A-Z0-9ÁÉÍÓÚÑ]+)*$/',
            'precio' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }
}
