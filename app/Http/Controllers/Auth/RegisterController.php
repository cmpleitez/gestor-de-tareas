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
use Illuminate\Support\Facades\Storage;
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
        if (! auth()->check() || ! auth()->user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permisos para acceder a esta página.');
        }
        return view('auth.register');
    }

    public function store(Request $request)
    {
        if (! auth()->check() || ! auth()->user()->hasRole('admin')) { // Verificar que el usuario esté autenticado y tenga rol Admin
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }
        $validated = Validator::make($request->all(), [ // Validación
            'name'               => ['required', 'string', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/'],
            'email'              => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'dui'                => ['required', 'string', Rule::unique('users', 'dui'), new ValidDui],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'terms'              => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'profile_photo_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:512'],
        ], [
            'profile_photo_path.max'   => 'La imagen no debe superar los 512KB.',
            'profile_photo_path.mimes' => 'Solo se permiten imágenes en formato JPEG o PNG.',
            'profile_photo_path.image' => 'El archivo debe ser una imagen válida.',
        ])->validate();
        try {
            DB::beginTransaction();
            $validated['password'] = Hash::make($validated['password']); // Crear usuario
            $generator = new CorrelativeIdGenerator();
            $id        = $generator->generate('User');
            $user      = new User();
            $user->fill($validated);
            $user->id = $id;
            $user->save();
            if (isset($request->profile_photo_path) && $request->profile_photo_path->isValid()) { // Procesar imagen de perfil si existe
                $imageFile                = $request->profile_photo_path;
                $imageName                = $user->id . '.' . $imageFile->getClientOriginalExtension();
                $path                     = $request->profile_photo_path->storeAs('profile-photos', $imageName, 'public');
                $user->profile_photo_path = $path;
                $user->save();
                try {
                    $fullPath = storage_path('app/public/' . $path);
                    $manager  = new ImageManager(Driver::class);
                    $image    = $manager->read($fullPath);
                    $image->scale(width: 64, height: 96);
                    $image->save($fullPath, quality: 60);
                } catch (Exception $e) {
                    Storage::disk('public')->delete($path);
                    throw new Exception('Error al procesar la imagen: ' . $e->getMessage());
                }
            }
            $user->assignRole('cliente'); // Asignar rol de Cliente
            $clienteRole = \Spatie\Permission\Models\Role::where('name', 'cliente')->first();
            if ($clienteRole) {
                $user->role_id = $clienteRole->id;
                $user->save();
            }
            $user->sendEmailVerificationNotification(); // Enviar correo de verificación
            DB::commit();
            return redirect('user')->with('success', 'Nuevo usuario registrado con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }
    }
}
