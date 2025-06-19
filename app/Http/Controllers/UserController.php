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

        return $request->all();

        //VALIDANDO
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^(?! )[a-zA-ZáéíóúÁÉÍÓÚ]+( [a-zA-ZáéíóúÁÉÍÓÚ]+)*$/',
            'email' => ['email', 'max:255', Rule::unique('users', 'email')],
            'dui' => [ // Este campo se valida sin guion para formato y algoritmo
                'required',
                'string',
                'regex:/^\d{9}$/',
                Rule::unique('users', 'dui'),
                function ($attribute, $value, $fail) {
                    if (!$this->isValidDui($value)) {
                        $fail('No es un DUI válido');
                    }
                },
            ],
            'oficina_id' => 'required|numeric|exists:oficinas,id',
            'password' => 'required|string|min:6|max:16',
            'password_confirmation' => 'required|string|min:6|max:16|same:password',
            'profile_photo_path' => 'nullable|image|max:1024',
        ]);


        return $validated;

        //GUARDANDO
        unset($validated['password_confirmation']);
        $validated['password'] = Hash::make($validated['password']);
        try {
            DB::beginTransaction();
            $user = User::create($validated);
            $user->sendEmailVerificationNotification(); // enviar correo de verificacion
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
            $user->assignRole('Beneficiario');
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error de base de datos al guardar usuario: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'Error al guardar el usuario (problema de base de datos): ' . $e->getMessage()]);
        } catch (TransportExceptionInterface $e) { // Captura errores de conexión o envío de correo para Laravel 9+
            DB::rollBack();
            Log::error('Error de envío de correo al guardar usuario: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'Error al guardar el usuario. No se pudo establecer conexión para enviar el correo de verificación. Por favor, intente nuevamente más tarde.']);
        } catch (Exception $e) { // Captura cualquier otra excepción inesperada
            DB::rollBack();
            Log::error('Error inesperado al guardar usuario: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'Ocurrió un error inesperado al guardar el usuario: ' . $e->getMessage()]);
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
        // === PASO 1: Pre-procesar el DUI antes de la validación ===
        $duiClean = str_replace('-', '', $request->input('dui'));
        $request->merge(['dui_clean' => $duiClean]);

        //VALIDANDO
        $validated = $request->validate([
            'email' => ['email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'dui' => [ // Este campo se valida con guion para formato y algoritmo
                'required',
                'string',
                'regex:/^(\d{8})-(\d)$/',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidDui($value)) {
                        $fail('El número de D.U.I no es válido o no tiene el formato adecuado. Ejemplo de formato: 99999999-9 (Incluya el guion)');
                    }
                },
            ],
            // === PASO 2: Validar unicidad usando el campo 'dui_clean' contra la columna 'dui' de la tabla 'users' ===
            'dui_clean' => ['required', 'string', 'digits:9', Rule::unique('users', 'dui')->ignore($user->id)], // Valida unicidad sin guion
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
        // === PASO 4: Usar el 'dui_clean' validado para guardar en la base de datos ===
        $validated['dui'] = $validated['dui_clean']; // Asigna el DUI sin guion al campo 'dui' para la actualización del modelo
        unset($validated['dui_clean']); // Ya no necesitamos el campo temporal 'dui_clean'

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
        return redirect()->route('user')->with('success', 'El usuario "' . $user->name . '" ha sido eliminado con éxito.');
    }

    public function activate(User $user)
    {
        $user->activo = !$user->activo;
        $user->save();
        return redirect()->route('user')->with('success', 'El usuario "' . $user->name . '" ha sido ' . ($user->activo ? 'activado' : 'desactivado') . ' correctamente');
    }

    private function isValidDui(string $dui): bool
    {
        // Validar que sean exactamente 9 dígitos
        if (!preg_match('/^\d{9}$/', $dui)) {
            return false;
        }

        // Extraer los primeros 8 dígitos y el dígito verificador
        $digits = substr($dui, 0, 8);
        $checkDigit = intval(substr($dui, -1));

        // Cálculo del dígito verificador
        $sum = 0;
        for ($i = 0; $i < strlen($digits); $i++) {
            $digit = intval($digits[$i]);
            $sum += (9 - $i) * $digit;
        }
        
        $calculatedCheckDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === $calculatedCheckDigit;
    }
}
