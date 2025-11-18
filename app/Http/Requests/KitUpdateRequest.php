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
            'kit' => ['required', 'min:3', 'max:128', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/', Rule::unique('kits')->ignore($kit->id)],
        ];
    }
}
