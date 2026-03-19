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
        if ($this->has("precio")) {
            $precio = $this->input("precio");
            $precio = preg_replace("/[^0-9.]/", "", $precio);
            $this->merge([
                "precio" => $precio !== "" ? (float) $precio : null,
            ]);
        }
    }

    public function rules(): array
    {
        $producto = $this->route("producto");
        return [
            "producto" => [
                "required",
                "min:3",
                "max:255",
                'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/',
                Rule::unique("productos")->ignore($producto->id),
            ],
            "codigo" => [
                "nullable",
                "min:3",
                "max:255",
                'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]+)*$/',
                Rule::unique("productos")->ignore($producto->id),
            ],
            "tipo_id" => ["required", "exists:tipos,id"],
            "modelo_id" => ["required", "exists:modelos,id"],
            "precio" => [
                "required",
                "numeric",
                "min:0",
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
        ];
    }

    public function withValidator($validator): void // Bloquea el cambio de nombre si el producto ya tiene historial
    {
        $validator->after(function ($validator) {
            $producto       = $this->route('producto');
            $nombreCambio   = $this->input('producto') !== $producto->producto;
            $tieneHistorial = $producto->kits()->exists() 
            || $producto->movimientos()->exists() 
            || $producto->oficinaStock->exists() 
            || $producto->detalles()->exists() 
            || $producto->equivalentes()->exists();

            if ($nombreCambio && $tieneHistorial) {
                $validator->errors()->add('producto', 'No se puede cambiar el nombre del producto porque ya tiene kits, movimientos, stocks, ordenes de compra o producto equivalentes asociados.');
            }
        });
    }
}
