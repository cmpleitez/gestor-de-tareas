<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kit' => 'required|unique:kits|min:3|max:128|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/',
        ];
    }
}
