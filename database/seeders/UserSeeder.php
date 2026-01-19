<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        //OFICINAS
        DB::table('oficinas')->insert([
            'id'         => 1,
            'oficina'    => 'Oficina Sede',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //EQUIPOS
        DB::table('equipos')->insert([
            'id'         => 1,
            'oficina_id' => 1,
            'equipo'     => 'Personal de atención al cliente',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE PERMISOS
        $permissions = [
            'ver',
            'crear',
            'editar',
            'asignar',
            'eliminar',
            'activar',
            'autorizar',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //ROLES Y ASIGNACIÓN DE PERMISOS
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $role = Role::create(['name' => 'superadmin']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);

        $role = Role::create(['name' => 'receptor']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'asignar']);

        $role = Role::create(['name' => 'supervisor']);
        $role->givePermissionTo(['ver', 'asignar']);

        $role = Role::create(['name' => 'gestor']);
        $role->givePermissionTo(['ver']);

        $role = Role::create(['name' => 'operador']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'asignar']);

        $role = Role::create(['name' => 'cliente']);
        $role->givePermissionTo(['ver', 'crear', 'editar']);

        //SUPERADMINISTRADOR
        DB::table('users')->insert([
            'id'                => 1,
            'role_id'           => 1,
            'name'              => 'superadmin',
            'dui'               => '012345678',
            'email'             => 'superadmin@servidor.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('p@5t15al5abana'),
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
        try {
            $user = User::findOrFail(1);
            $user->assignRole('superadmin');
        } catch (\Exception $e) {
            echo "Error asignando rol superadmin: " . $e->getMessage();
        }

        //ADMINISTRADOR
        DB::table('users')->insert([
            'id'                => 2,
            'role_id'           => 2,
            'name'              => 'admin',
            'dui'               => '023456783',
            'email'             => 'admin@servidor.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('p@5t15al5abana'),
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
        try {
            $user = User::findOrFail(2);
            $user->assignRole('admin');
        } catch (\Exception $e) {
            echo "Error asignando rol admin: " . $e->getMessage();
        }

        //CREACION DE TAREAS
        DB::table('tareas')->insert([
            'id'         => 1,
            'tarea'      => 'Despacho iniciado',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'id'         => 2,
            'tarea'      => 'Revisión',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'id'         => 3,
            'tarea'      => 'Verificación física',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'id'         => 4,
            'tarea'      => 'Descarga de stock',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'id'         => 5,
            'tarea'      => 'Entrega de productos',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE SOLICITUDES
        DB::table('solicitudes')->insert([
            'id'         => 1,
            'solicitud'  => 'Revisión de la orden de compra',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('solicitudes')->insert([
            'id'         => 2,
            'solicitud'  => 'Verificación física del stock',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE ESTADOS
        DB::table('estados')->insert([
            'id'         => 1,
            'estado'     => 'Solicitada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'id'         => 2,
            'estado'     => 'Recibida',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'id'         => 3,
            'estado'     => 'En progreso',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'id'         => 4,
            'estado'     => 'Resuelta',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE PARAMETROS
        DB::table('parametros')->insert([
            'id'           => 1,
            'parametro'  => 'Frecuencia de refresco',
            'valor'      => '1',
            'unidad_medida' => 'minutos',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('parametros')->insert([
            'id'           => 2,
            'parametro'  => 'Nombres de kits automáticos',
            'valor'      => '1',
            'unidad_medida' => 'boolean',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    }
}
