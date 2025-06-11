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

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Oficina;
use App\Models\Equipo;

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
            'email' => ['email', 'max:255', Rule::unique('users', 'email')],
            'oficina_id' => 'required|numeric|exists:oficinas,id',
            'password' => 'required|string|min:6|max:16',
            'password_confirmation' => 'required|string|min:6|max:16|same:password',
            'profile_photo_path' => 'nullable|image|max:1024',
        ]);
        //GUARDANDO
        unset($validated['password_confirmation']);
        $validated['password'] = Hash::make($validated['password']);
        try {
            DB::beginTransaction();
            $user = User::create($validated);
            if ($request->hasfile('profile_photo_path')) {
                $nombre_archivo = $user->id . '.' . $request->file('profile_photo_path')->extension();
                $ruta_destino = 'public/' . auth()->user()->oficina_id;
                $user->profile_photo_path = Storage::putFileAs($ruta_destino, $request->file('profile_photo_path'), $nombre_archivo);
                $user->save();
                $manager = new ImageManager(Driver::class);
                $image = $manager->read(Storage::get($user->profile_photo_path));
                $image->cover(150, 200, 'center');
                $image->save(Storage::path($user->profile_photo_path));
            }
            $user->assignRole('Operador');
            $user->sendEmailVerificationNotification(); //enviar correo de verificacion
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar el usuario: ' . $e->getMessage()]);
        }
        return redirect()->route('user')->with('success', 'El nuevo operador ' . $user->name . ' ha sido registrado efectivamente. Se ha enviado un correo de verificación.');
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
        que su correo ha cambiado, se le ha enviado una solicitud de verificación su nuevo correo para su respectiva
        validación.' : 'El usuario ha sido actualizado con éxito.';
        //GUARDANDO
        try {
            DB::beginTransaction();
            if ($correo_actualizado) {
                $user->sendEmailVerificationNotification(); //enviar correo de verificacion
            }
            $user->update($validated);
            if ($request->hasfile('profile_photo_path')) {
                $nombre_archivo = $user->id . '.' . $request->file('profile_photo_path')->extension();
                $ruta_destino = 'public/' . auth()->user()->oficina_id;
                $user->profile_photo_path = Storage::putFileAs($ruta_destino, $request->file('profile_photo_path'), $nombre_archivo);
                $user->save();
                $manager = new ImageManager(Driver::class);
                $image = $manager->read(Storage::get($user->profile_photo_path));
                $image->cover(150, 200, 'center');
                $image->save(Storage::path($user->profile_photo_path));
            }
            DB::commit();
            return redirect()->route('user')->with('success', $mensaje);
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar el usuario: ' . $e->getMessage()]);
        } catch (\Exception $e) {
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
        $submittedRoles = $request->input('roles', []);
        if ($user->hasRole('SuperAdmin')) {
            if (!in_array('SuperAdmin', $submittedRoles)) {
                $submittedRoles[] = 'SuperAdmin';
            }
        }
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
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el usuario: ' . $e->getMessage());
        }
        return redirect()->route('user')->with('success', 'El usuario ha sido eliminado con éxito.');
    }
}
