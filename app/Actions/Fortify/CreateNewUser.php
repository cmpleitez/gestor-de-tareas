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

use App\Models\User;
use App\Rules\ValidDui;
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'dui' => ['required', 'string', new ValidDui],
            'oficina_id' => ['required', 'exists:oficinas,id'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'profile_photo_path' => ['nullable', 'image', 'max:10240'],
        ])->validate();

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'dui' => $input['dui'],
                'oficina_id' => $input['oficina_id'],
                'password' => Hash::make($input['password']),
            ]);
            if (isset($input['profile_photo_path']) && $input['profile_photo_path']->isValid()) {
                ini_set('max_execution_time', 60); // Aumentar límites de tiempo y memoria
                ini_set('memory_limit', '256M');
                $imageFile = $input['profile_photo_path'];
                if ($imageFile->getSize() > 5 * 1024 * 1024) { // Validar tamaño del archivo (máximo 5MB)
                    throw new \Exception('El archivo de imagen es demasiado grande. Máximo 5MB permitido.');
                }
                $imageName = $user->id . '.' . $imageFile->getClientOriginalExtension();
                if (!Storage::disk('public')->exists('profile-photos')) { // Crear directorio si no existe
                    Storage::disk('public')->makeDirectory('profile-photos');
                }
                $path = Storage::disk('public')->putFileAs('profile-photos', $input['profile_photo_path'], $imageName); // Guardar la imagen en el repositorio
                try { // Procesar la imagen
                    $fullPath = Storage::disk('public')->path($path);
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($fullPath);
                    $image->scale(width: 150, height: 200);
                    $image->save($fullPath);
                } catch (\Exception $e) {
                    Storage::disk('public')->delete($path);
                    throw new \Exception('Error al procesar la imagen: ' . $e->getMessage());
                }
                $user->profile_photo_path = $path;
                $user->save(); // Guardar el usuario en la base de datos
            }
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'NotReadable') !== false) {
                Storage::delete($path);
                throw new \Exception('Error: La imagen no es válida o no se puede decodificar. ' . $e->getMessage());
            }            
            throw new \Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }
}
