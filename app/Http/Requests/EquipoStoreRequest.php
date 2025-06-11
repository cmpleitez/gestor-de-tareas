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
            'equipo' => 'required|unique:equipos|min:3|max:128|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/',
        ];
    }
}
