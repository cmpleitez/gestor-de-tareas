<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitStoreRequest extends FormRequest
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
    }

    public function rules(): array
    {
        return [
            'kit' => 'required|unique:kits|min:3|max:255|regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/',
            'precio' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ];
    }
}
