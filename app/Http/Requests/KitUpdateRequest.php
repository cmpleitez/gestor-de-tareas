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
            'kit' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('kits')->ignore($kit->id)],
            'producto' => ['required', 'array'],
            'producto.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'producto.*.unidades' => ['required', 'integer', 'min:1'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si el kit ya tiene historial
    {
        $validator->after(function ($validator) {
            $kit            = $this->route('kit');
            $nombreCambio   = $this->input('kit') !== $kit->kit;
            $tieneHistorial = $kit->ordenes()->exists() || $kit->detalles()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('kit', 'No se puede cambiar el nombre del kit porque ya tiene ordenes de compras.');
            }
        });
    }
}

