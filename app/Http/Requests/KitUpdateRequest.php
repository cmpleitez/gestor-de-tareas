<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KitUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kit = $this->route('kit');
        return [
            'kit' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/', Rule::unique('kits')->ignore($kit->id)],
            'producto' => ['required', 'array'],
            'producto.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'producto.*.unidades' => ['required', 'integer', 'min:1'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ];
    }
}
