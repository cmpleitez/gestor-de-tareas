<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitudUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $solicitud = $this->route('solicitud');
        return [
            'solicitud' => ['required', 'min:3', 'max:128', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ()]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ()]+)*$/', Rule::unique('solicitudes')->ignore($solicitud->id)],
        ];
    }
}
