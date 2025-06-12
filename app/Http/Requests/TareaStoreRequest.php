<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TareaStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tarea' => 'required|unique:tareas|min:3|max:128|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/',
        ];
    }
}
