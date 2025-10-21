<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\ValidDui;
use App\Services\CorrelativeIdGenerator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Laravel\Jetstream\Jetstream;

class RegisterController extends Controller
{
    public function create()
    {
        // Verificar que el usuario esté autenticado y tenga rol Admin
        if (! auth()->check() || ! auth()->user()->hasRole('admin|superadmin')) {
            return back()->with('error', 'No tienes permisos para acceder a esta página.');
        }
        return view('auth.register');
    }

    public function store(Request $request)
    {
        if (! auth()->check() || ! auth()->user()->hasRole('admin|superadmin')) { // Verificar que el usuario esté autenticado y tenga rol Admin
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $validated = Validator::make($request->all(), [ // Validación
            'name'               => ['required', 'string', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/'],
            'email'              => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'dui'                => ['required', 'string', Rule::unique('users', 'dui'), new ValidDui],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'terms'              => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'profile_photo_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ], [
            'profile_photo_path.mimes' => 'Solo se permiten imágenes en formato JPG, JPEG o PNG.',
            'profile_photo_path.max'   => 'El archivo no debe exceder de 1 MB.',
            'profile_photo_path.image' => 'El archivo debe ser una imagen válida.',
            'name.regex' => 'El nombre no debe contener espacios en blanco al inicio o al final.',
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder de 255 caracteres.',
            'email.required' => 'El email es requerido.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El email debe ser una dirección de email válida.',
            'email.max' => 'El email no debe exceder de 255 caracteres.',
            'email.unique' => 'El email ya está registrado.',
            'dui.required' => 'El DUI es requerido.',
            'dui.string' => 'El DUI debe ser una cadena de texto.',
            'dui.unique' => 'El DUI ya está registrado.',
            'password.required' => 'La contraseña es requerida.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La contraseña no coincide.',
        ])->validate();
        try {
            DB::beginTransaction();
            $validated['password'] = Hash::make($validated['password']); // Crear usuario
            $generator             = new CorrelativeIdGenerator();
            $id                    = $generator->generate('User');
            $user                  = new User();
            $user->fill($validated);
            $user->id = $id;
            $user->save();
            if (isset($request->profile_photo_path) && $request->profile_photo_path->isValid()) { // Procesar imagen de perfil si existe
                try {
                    $imageFile = $request->profile_photo_path;
                    $imageName = $user->id . '.' . $imageFile->getClientOriginalExtension();

                    // Procesar imagen ANTES de guardar para hacerlo más rápido
                    $manager = new ImageManager(Driver::class);
                    $image   = $manager->read($imageFile->getRealPath());
                    $image->scale(width: 200); // Escalar a máximo 200px de ancho (mantiene proporción)

                    // Guardar imagen ya optimizada
                    $storagePath = storage_path('app/public/profile-photos');
                    if (! file_exists($storagePath)) {
                        mkdir($storagePath, 0775, true);
                    }
                    $fullPath = $storagePath . '/' . $imageName;
                    $image->save($fullPath, quality: 75);

                    // Actualizar path en BD
                    $user->profile_photo_path = 'profile-photos/' . $imageName;
                    $user->save();
                } catch (Exception $e) {
                    throw new Exception('Error al procesar la imagen: ' . $e->getMessage());
                }
            }
            $user->assignRole('cliente'); // Asignar rol de Cliente
            $clienteRole = \Spatie\Permission\Models\Role::where('name', 'cliente')->first();
            if ($clienteRole) {
                $user->role_id = $clienteRole->id;
                $user->save();
            }
            $user->sendEmailVerificationNotification(); // Enviar correo de verificación - Comentado temporalmente para evitar timeout
            DB::commit();
            return redirect('user')->with('success', 'Nuevo usuario registrado con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }
    }
}
