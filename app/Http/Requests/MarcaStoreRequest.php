<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class MarcaStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'marca' => 'required|unique:marcas|min:3|max:255|regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/',
        ];
    }
}
