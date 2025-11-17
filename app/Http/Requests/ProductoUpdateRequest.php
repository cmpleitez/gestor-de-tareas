<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoUpdateRequest extends FormRequest
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
        $producto = $this->route('producto');
        return [
            'producto' => ['required', 'min:3', 'max:128', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/', Rule::unique('productos')->ignore($producto->id)],
            'tipo_id' => ['required', 'exists:tipos,id'],
            'modelo_id' => ['required', 'exists:modelos,id'],
            'precio' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }
}
