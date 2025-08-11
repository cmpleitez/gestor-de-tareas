<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Exception;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Oficina;
use App\Models\Equipo;
use App\Models\Solicitud;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('area.oficina', 'equipos', 'roles')->whereNotIn('id', ['1'])->get();
        return view('modelos.user.index', compact('users'));
    }

    public function create()
    {
        $oficinas = Oficina::where('activo', true)->get();
        return view('modelos.user.create', ['oficinas' => $oficinas]);
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
            'email' => ['email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'oficina_id' => 'required|numeric|exists:oficinas,id',
            'profile_photo_path' => 'nullable|image|max:1024',
        ]);
        $correo_actualizado = false;
        if ($user->email != $validated['email']) {
            $validated['email_verified_at'] = null;
            $correo_actualizado = true;
        }
        $mensaje = ($correo_actualizado) ? 'Los datos del usuario han sido actualizados con éxito. Debido a 
        ue su correo ha cambiado, se le ha enviado una solicitud de verificación su nuevo correo para su respectiva
        validación.' : 'El usuario ha sido actualizado con éxito.';
        //GUARDANDO
        try {
            DB::beginTransaction();
            $user->update($validated); //Crear el registro en la base de datos
            ini_set('max_execution_time', 60);
            ini_set('memory_limit', '256M');
            if (isset($request['profile_photo_path']) && $request['profile_photo_path']->isValid()) {
                $imageFile = $request['profile_photo_path'];
                $imageName = $user->id . '.' . $imageFile->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('profile-photos', $request['profile_photo_path'], $imageName);
                $user->profile_photo_path = $path;
                $user->save(); //Actualizar el link en base de datos
                try {
                    $fullPath = Storage::disk('public')->path($path); //Adaptación de la imagen al perfil del usuario
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($fullPath);
                    $image->scale(width: 64, height: 96);
                    $image->save($fullPath);
                } catch (Exception $e) {
                    Storage::disk('public')->delete($path);
                    throw new Exception('Error al procesar la imagen: ' . $e->getMessage());
                }
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
        $roles = Role::where('name', '!=', 'SuperAdmin')->get();
        return view('modelos.user.roles-edit', ['user' => $user, 'roles' => $roles]);
    }

    public function rolesUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'role_id' => 'required|numeric|exists:roles,id',
        ]);
        $submittedRoles = $validated['roles'];
        if ($user->hasRole('SuperAdmin')) {
            if (!in_array('SuperAdmin', $submittedRoles)) {
                $submittedRoles[] = 'SuperAdmin';
            }
        }
        try {
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


    public function solicitudesEdit(User $user)
    {
        $solicitudes = Solicitud::where('activo', true)->get();
        return view('modelos.user.solicitudes-edit', ['user' => $user, 'solicitudes' => $solicitudes]);
    }

    public function solicitudesUpdate(Request $request, User $user)
    {
        $solicitudes = $request->input('solicitudes', []);
        $user->solicitudes()->sync($solicitudes);
        return redirect()->route('user')->with('success', 'Las habilidades de ' . $user->name . ' han sido actualizadas efectivamente.');
    }

    public function destroy(User $user)
    {
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
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
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
        $user->activo = !$user->activo;
        $user->save();
        return redirect()->route('user')->with('success', 'El usuario "' . $user->name . '" ha sido ' . ($user->activo ? 'activado' : 'desactivado') . ' correctamente');
    }

}
