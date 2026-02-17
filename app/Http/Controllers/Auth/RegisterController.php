<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\ValidDui;
use App\Services\CorrelativeIdGenerator;
use App\Services\ImageWeightStabilizer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            'name'               => ['required', 'string', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/'],
            'email'              => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'dui'                => ['required', 'string', Rule::unique('users', 'dui'), new ValidDui],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'terms'              => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ], [
            'image_path.mimes' => 'Solo se permiten imágenes en formato JPG, JPEG o PNG.',
            'image_path.max'   => 'El archivo no debe exceder de 5 MB.',
            'image_path.image' => 'El archivo debe ser una imagen válida.',
            'name.regex'               => 'Solo se permiten letras, sin espacios al inicio/final ni dobles espacios.',
            'name.required'            => 'Este campo es obligatorio.',
            'name.string'              => 'El nombre debe ser una cadena de texto.',
            'name.min'                 => 'El nombre debe tener al menos 3 caracteres.',
            'name.max'                 => 'El nombre no debe exceder de 255 caracteres.',
            'email.required'           => 'El email es requerido.',
            'email.string'             => 'El email debe ser una cadena de texto.',
            'email.email'              => 'Debe ser un correo electrónico válido.',
            'email.max'                => 'El email no debe exceder de 255 caracteres.',
            'email.unique'             => 'El email ya está registrado.',
            'dui.required'             => 'El DUI es requerido.',
            'dui.string'               => 'El DUI debe ser una cadena de texto.',
            'dui.unique'               => 'El DUI ya está registrado.',
            'password.required'        => 'La contraseña es requerida.',
            'password.string'          => 'La contraseña debe ser una cadena de texto.',
            'password.min'             => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'       => 'Las contraseñas no coinciden.',
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
            if (isset($request->image_path) && $request->image_path->isValid()) { // Procesar imagen de perfil si existe
                $imageStabilizer = new ImageWeightStabilizer();
                $imageStabilizer->stabilize(
                    $request->image_path,
                    storage_path('app/public/user-images'),
                    'User',
                    $user->id
                );
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
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error en registro de usuario: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['email' => 'Ocurrió un error al procesar el registro del usuario.'])->withInput();
        }
    }
}
