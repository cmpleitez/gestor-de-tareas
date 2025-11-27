<?php
namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\ValidDui;
use App\Services\ImageWeightStabilizer;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        //VALIDANDO
        $validated = Validator::make($input, [
            'name'               => ['required', 'string', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/'],
            'email'              => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'dui'                => ['required', 'string', Rule::unique('users', 'dui'), new ValidDui],
            'password'           => $this->passwordRules(),
            'terms'              => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:512'], // Solo JPEG/PNG, 512KB máximo
        ], [
            'image_path.max'   => 'La imagen no debe superar los 512KB.',
            'image_path.mimes' => 'Solo se permiten imágenes en formato JPEG o PNG.',
            'image_path.image' => 'El archivo debe ser una imagen válida.',
        ])->validate();

        //GUARDANDO
        try {
            DB::beginTransaction();
            $validated['password'] = Hash::make($validated['password']);
            $user                  = User::create($validated); //Crear el registro en la base de datos
            ini_set('max_execution_time', 60);
            ini_set('memory_limit', '256M');
            if (isset($input['image_path']) && $input['image_path']->isValid()) {
                $imageStabilizer = new ImageWeightStabilizer();
                $imageStabilizer->processProfilePhoto(
                    $input['image_path'],
                    storage_path('app/public/user-photos'),
                    'User',
                    $user->id
                );
            }
            $user->assignRole('Cliente'); //Asignar el rol de Cliente

            // También asignar el role_id para la relación mainRole
            $clienteRole = \Spatie\Permission\Models\Role::where('name', 'Cliente')->first();
            if ($clienteRole) {
                $user->role_id = $clienteRole->id;
                $user->save();
            }

            $user->sendEmailVerificationNotification();
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }
}
