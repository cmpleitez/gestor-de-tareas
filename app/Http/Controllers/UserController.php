<?php
namespace App\Http\Controllers;
use App\Services\ImageWeightStabilizer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;
use App\Models\Equipo;
use App\Models\Oficina;
use App\Models\Recepcion;
use App\Models\User;
use App\Models\Tarea;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('oficina', 'equipos', 'roles')->whereNotIn('id', ['1'])->get();
        return view('modelos.user.index', compact('users'));
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
            'name'               => ['sometimes', 'required', 'string', 'min:3', 'max:255', 'regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚñÑ]+( [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/'],
            'email'              => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'oficina_id'         => ['required', 'numeric', 'exists:oficinas,id'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ], [
            'name.required'            => 'Este campo es obligatorio.',
            'name.string'              => 'El nombre debe ser una cadena de texto.',
            'name.min'                 => 'El nombre debe tener al menos 3 caracteres.',
            'name.max'                 => 'El nombre no debe exceder de 255 caracteres.',
            'name.regex'               => 'Solo se permiten letras, sin espacios al inicio/final ni dobles espacios.',
            'email.required'           => 'El email es requerido.',
            'email.email'              => 'Debe ser un correo electrónico válido.',
            'email.max'                => 'El email no debe exceder de 255 caracteres.',
            'email.unique'             => 'El email ya está registrado.',
            'oficina_id.required'      => 'Este campo es obligatorio.',
            'oficina_id.numeric'       => 'La oficina debe ser un valor numérico.',
            'oficina_id.exists'        => 'La oficina seleccionada no es válida.',
            'image_path.image' => 'El archivo debe ser una imagen válida.',
            'image_path.mimes' => 'Solo se permiten imágenes en formato JPG, JPEG o PNG.',
            'image_path.max'   => 'El archivo no debe exceder de 5 MB.',
        ]);
        // Restringir cambio de name si existen recepciones asociadas como destino
        $incomingName = $request->input('name');
        if ($incomingName !== null && $incomingName !== $user->name) {
            if (Recepcion::where('destino_user_id', $user->id)->exists()) {
                return back()->with('error', 'No se puede cambiar el nombre porque el usuario tiene recepciones asignadas.');
            }
            $validated['name'] = $incomingName;
        }
        $correo_actualizado = false;
        if ($user->email != $validated['email']) {
            $validated['email_verified_at'] = null;
            $correo_actualizado             = true;
        }
        $mensaje = ($correo_actualizado) ? 'Los datos del usuario han sido actualizados con éxito. Debido a
        que su correo ha cambiado, se le ha enviado una solicitud de verificación su nuevo correo para su respectiva
        validación.' : 'El usuario ha sido actualizado con éxito.';
        //GUARDANDO
        try {
            DB::beginTransaction();
            $user->update($validated); //Crear el registro en la base de datos
            ini_set('max_execution_time', 60);
            ini_set('memory_limit', '256M');
            if (isset($request['image_path']) && $request['image_path']->isValid()) {
                $imageStabilizer = new ImageWeightStabilizer();
                $imageStabilizer->stabilize(
                    $request['image_path'],
                    storage_path('app/public/user-images'),
                    'User',
                    $user->id
                );
            }
            DB::commit();
            return redirect()->route('user')->with('success', $mensaje);
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar el usuario: ' . $e->getMessage()]);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar la imagen: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function rolesEdit(User $user)
    {
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('modelos.user.roles-edit', ['user' => $user, 'roles' => $roles]);
    }

    public function rolesUpdate(Request $request, User $user)
    {
        try {
            //VALIDACIÓN
            $validated = $request->validate([
                'roles'   => 'required|array',
                'role_id' => 'required|numeric|exists:roles,id',
            ]);
            $submittedRoles = $validated['roles'];
                                             // Validación para rol de cliente
            if ($user->hasRole('Cliente')) { // Usuario que ya es cliente
                if (in_array('Cliente', $submittedRoles) && count($submittedRoles) > 1) {
                    throw new Exception('No esta disponible la funcionalidad de ser cliente y otro rol a la vez');
                }
            } elseif (in_array('Cliente', $submittedRoles)) { // Usuario que se está convirtiendo en cliente
                if (count($submittedRoles) > 1) {
                    throw new Exception('No esta disponible la funcionalidad de ser cliente y otro rol a la vez');
                }
            }
            if ($user->hasRole('admin')) {
                if (! in_array('admin', $submittedRoles)) {
                    $submittedRoles[] = 'admin';
                }
            }
            if (in_array($user->mainRole->name, ['admin']) && $validated['role_id'] != $user->role_id) {
                throw new Exception('No se puede cambiar el rol principal de un usuario ' . $user->mainRole->name);
            }
            //PROCESO
            DB::beginTransaction();
            $user->syncRoles($submittedRoles);
            $roleId = $validated['role_id'];
            if ($roleId) {
                $user->role_id = $roleId;
                $user->save();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocurrió un error cuando se intentaba guardar los cambios: ' . $e->getMessage());
        }
        return redirect()->route("user")->with('success', 'Los roles para el usuario ' . $user->name . ' han sido actualizados efectivamente.');
    }

    public function equiposEdit(User $user)
    {
        $equipos = Equipo::where('activo', true)->get();
        return view('modelos.user.equipos-edit', ['user' => $user, 'equipos' => $equipos]);
    }

    public function equiposUpdate(Request $request, User $user)
    {
        $equipos = $request->input('equipos', []);
        $user->equipos()->sync($equipos);
        return redirect()->route('user')->with('success', 'Los equipos para el usuario ' . $user->name . ' han sido actualizados efectivamente.');
    }

    public function tareasEdit(User $user)
    {
        $tareas = Tarea::where('activo', true)->get();
        return view('modelos.user.tareas-edit', ['user' => $user, 'tareas' => $tareas]);
    }

    public function tareasUpdate(Request $request, User $user)
    {
        $tareas = $request->input('tareas', []);
        $user->tareas()->sync($tareas);
        return redirect()->route('user')->with('success', 'Las habilidades de ' . $user->name . ' han sido actualizadas efectivamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo por seguridad.');
        }

        if ($user->equipos()->exists()) {
            return back()->with('error', 'El usuario no puede ser eliminado porque tiene equipos asignados.');
        }
        if ($user->solicitudesRecibidas()->exists()) {
            return back()->with('error', 'El usuario no puede ser eliminado porque tiene solicitudes recibidas.');
        }
        if ($user->solicitudesEnviadas()->exists()) {
            return back()->with('error', 'El usuario no puede ser eliminado porque tiene solicitudes enviadas.');
        }
        try {
            DB::beginTransaction();
            $user->delete();
            if ($user->image_path) {
                Storage::disk('public')->delete($user->image_path);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el usuario: ' . $e->getMessage());
        }
        return redirect()->route('user')->with('success', 'El usuario "' . $user->name . '" ha sido eliminado con éxito.');
    }

    public function activate(User $user)
    {
        $user->activo = ! $user->activo;
        $user->save();
        return redirect()->route('user')->with('success', 'El usuario "' . $user->name . '" ha sido ' . ($user->activo ? 'activado' : 'desactivado') . ' correctamente');
    }

}
