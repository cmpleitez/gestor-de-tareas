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
        // Verificar que el usuario esté autenticado y tenga rol SuperAdmin
        if (! auth()->check() || ! auth()->user()->hasRole('SuperAdmin')) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return view('auth.register');
    }

    public function store(Request $request, CreateNewUser $createNewUser)
    {
        // Verificar que el usuario esté autenticado y tenga rol SuperAdmin
        if (! auth()->check() || ! auth()->user()->hasRole('SuperAdmin')) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        try {
            $user = $createNewUser->create($request->all());

            return redirect('/dashboard')->with('success', 'Nuevo usuario registrado con éxito y una solicitud de verificación de correo electrónico ha sido enviada al nuevo usuario.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
