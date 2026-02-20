<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TareaUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tarea = $this->route('tarea');
        return [
            'tarea' => ['required', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+( [a-zA-Z0-9áéíóúÁÉÍÓÚñÑ()-]+)*$/', Rule::unique('tareas')->ignore($tarea->id)],
        ];
    }
}
