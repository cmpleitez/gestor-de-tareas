<?php
namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RegisterController extends Controller
{

    /**
     * Mostrar el formulario de registro
     */
    public function create()
    {
        // Verificar que el usuario esté autenticado y tenga rol Admin
        if (! auth()->check() || ! auth()->user()->hasRole('Admin')) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return view('auth.register');
    }

    public function store(Request $request, CreateNewUser $createNewUser)
    {
        // Debug: Verificar si llega al controlador
        file_put_contents(storage_path('debug_register.txt'),
            "=== REGISTRO DEBUG ===\n" .
            "Datos recibidos: " . json_encode($request->all()) . "\n" .
            "Timestamp: " . now() . "\n\n",
            FILE_APPEND
        );

        // Verificar que el usuario esté autenticado y tenga rol Admin
        if (! auth()->check() || ! auth()->user()->hasRole('Admin')) {
            file_put_contents(storage_path('debug_register.txt'),
                "ERROR: Usuario no autenticado o sin rol Admin\n",
                FILE_APPEND
            );
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        try {
            $user = $createNewUser->create($request->all());

            file_put_contents(storage_path('debug_register.txt'),
                "Usuario creado exitosamente: ID=" . $user->id . ", Email=" . $user->email . "\n",
                FILE_APPEND
            );

            return redirect('/dashboard')->with('success', 'Nuevo usuario registrado con éxito y una solicitud de verificación de correo electrónico ha sido enviada al nuevo usuario.');
        } catch (\Exception $e) {
            file_put_contents(storage_path('debug_register.txt'),
                "ERROR: " . $e->getMessage() . "\n\n",
                FILE_APPEND
            );
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }
    }
}
