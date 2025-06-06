<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Oficina;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNotIn('id', ['1'])->get();
        return view('modelos.user.index', compact('users'));
    }

    public function create()
    {
        $oficinas = Oficina::where('activo', true)->get();
        return view('modelos.user.create', ['oficinas' => $oficinas]);
    }

    public function store(Request $request)
    {
        //VALIDANDO
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/',
            'email'     => ['email', 'max:255', Rule::unique('users', 'email')],
            'oficina_id' => 'required|numeric|exists:oficinas,id',
            'password' => 'required|string|min:6|max:16',
            'password_confirmation' => 'required|string|min:6|max:16|same:password',
        ]);

        //GUARDANDO
        unset($validated['password_confirmation']);
        $validated['password'] = Hash::make($validated['password']);
        try {
            DB::beginTransaction();
            $user = User::create($validated);
            $user->assignRole('Operador');
            DB::commit();
            return redirect()->route("user")->with('success', 'El nuevo operador ' . $user->name . ' ha sido registrado efectivamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar el usuario: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(User $user)
    {
        $oficinas = Oficina::where('activo', true)->get();
        return view('modelos.user.edit', ['user' => $user, 'oficinas' => $oficinas]);
    }

    public function update(Request $request, User $user)
    {
        //VALIDANDO
        $validated = $request->validate([
            'email'     => ['email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'oficina_id' => 'required|numeric|exists:oficinas,id',        
        ]);
        $correo_actualizado = false;
        if ($user->email != $validated['email']) {
            $validated['email_verified_at'] = null;
            $correo_actualizado = true;
        }
        $mensaje = ($correo_actualizado) ? 'Los datos del usuario han sido actualizados con éxito. Debido a 
        que su correo ha cambiado, se le ha enviado una solicitud de verificación su nuevo correo para su respectiva
        validación.' : 'El usuario ha sido actualizado con éxito.';        
        //GUARDANDO
        try {
            $user->update($validated);
            return redirect()->route("user")->with('success', 'El nuevo operador ' . $user->name . ' ha sido registrado efectivamente.');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Error al guardar el usuario: ' . $e->getMessage()]);
        }
        //RESPUESTA
        return redirect()->route('user')->with('success', $mensaje);
    }

    public function rolesEdit(User $user)
    {
        $roles = Role::where('name', '!=', 'SuperAdmin')->get();
        return view('modelos.user.roles-edit', ['user' => $user, 'roles' => $roles]);
    }

    public function rolesUpdate(Request $request, User $user)
    {
        // Obtener los roles seleccionados en el formulario. Usa un array vacío si no se seleccionó ninguno.
        $submittedRoles = $request->input('roles', []);

        // Si el usuario que se está actualizando es un SuperAdmin, asegurar que el rol 'SuperAdmin' esté en la lista
        if ($user->hasRole('SuperAdmin')) {
            // Verificar si 'SuperAdmin' ya está en la lista enviada (aunque el formulario no lo envíe explícitamente,
            // esta verificación es una buena práctica si el formulario pudiera cambiar en el futuro)
            if (!in_array('SuperAdmin', $submittedRoles)) {
                // Si no está, añadirlo a la lista de roles a sincronizar
                $submittedRoles[] = 'SuperAdmin';
            }
        }

        // Sincronizar los roles del usuario con la lista (incluyendo 'SuperAdmin' si se agregó)
        try {
            DB::beginTransaction();
            $user->syncRoles($submittedRoles);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocurrió un error cuando se intentaba guardar los cambios: ' . $e->getMessage());
        }
        return redirect()->route("user")->with('success', 'Los roles para el usuario ' . $user->name . ' han sido actualizados efectivamente.');
    }

    public function destroy(User $user)
    {
        if ($user->equipos->count() > 0) {
            return back()->with('error', 'El usuario no puede ser eliminado porque tiene equipos asignados.');
        }
        if ($user->tareas_usuario_origen->count() > 0) {
            return back()->with('error', 'El usuario no puede ser eliminado porque ya ha delegado tareas.');
        }
        if ($user->tareas_usuario_destino->count() > 0) {
            return back()->with('error', 'El usuario no puede ser eliminado porque tiene tareas asignadas.');
        }
        try {
            $user->delete();
            return redirect()->route("user")->with('success', 'El usuario ' . $user->name . ' ha sido eliminado efectivamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el usuario: ' . $e->getMessage());
        }
    }
}
