<?php
namespace App\Actions\Fortify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Models\User;
use App\Rules\ValidDui;
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        // DEPURACIÓN: mostrar el input recibido
        //dd($input); // Detiene la ejecución y muestra el contenido recibido


        //VALIDANDO
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'dui' => ['required', 'string', Rule::unique('users', 'dui'), new ValidDui],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'profile_photo_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:512'], // Solo JPEG/PNG, 512KB máximo
        ], [
            'profile_photo_path.max' => 'La imagen no debe superar los 512KB.',
            'profile_photo_path.mimes' => 'Solo se permiten imágenes en formato JPEG o PNG.',
            'profile_photo_path.image' => 'El archivo debe ser una imagen válida.'
        ])->validate();

        //dd($validated); // Depuración: mostrar el array validado

        //GUARDANDO
        try {
            DB::beginTransaction();
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated); //Crear el registro en la base de datos
            ini_set('max_execution_time', 60);
            ini_set('memory_limit', '256M');
            if (isset($input['profile_photo_path']) && $input['profile_photo_path']->isValid()) {
                $imageFile = $input['profile_photo_path'];
                $imageName = $user->id . '.' . $imageFile->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('profile-photos', $input['profile_photo_path'], $imageName);
                $user->profile_photo_path = $path;
                $user->save(); //Actualizar el link en base de datos
                try {
                    $fullPath = Storage::disk('public')->path($path); //Adaptación de la imagen al perfil del usuario
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($fullPath);
                    $image->scale(width: 64, height: 96);
                    $image->save($fullPath, quality: 60); // Reducir calidad a 60% para archivos muy pequeños
                } catch (Exception $e) {
                    Storage::disk('public')->delete($path);
                    throw new Exception('Error al procesar la imagen: ' . $e->getMessage());
                }
            }
            $user->assignRole('Beneficiario'); //Asignar el rol de Beneficiario
            DB::commit();
            return $user;
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'NotReadable') !== false) {
                Storage::delete($path);
                throw new Exception('Error: La imagen no es válida o no se puede decodificar. ' . $e->getMessage());
            }
            throw new Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }
}
