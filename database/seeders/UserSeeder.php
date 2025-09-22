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
            'oficina'    => 'Oficina Sede',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //EQUIPOS
        DB::table('equipos')->insert([
            'oficina_id' => 1,
            'equipo'     => 'Técnicos en suspensión',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('equipos')->insert([
            'oficina_id' => 1,
            'equipo'     => 'Técnicos en luces adaptativas',
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
        $role = Role::create(['name' => 'SuperAdmin']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);

        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);

        $role = Role::create(['name' => 'Receptor']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'asignar']);

        $role = Role::create(['name' => 'Supervisor']);
        $role->givePermissionTo(['ver', 'asignar']);

        $role = Role::create(['name' => 'Gestor']);
        $role->givePermissionTo(['ver']);

        $role = Role::create(['name' => 'Operador']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'asignar']);

        $role = Role::create(['name' => 'Cliente']);
        $role->givePermissionTo(['ver', 'crear', 'editar']);

        //USUARIOS
        DB::table('users')->insert([
            'role_id'           => 1,
            'name'              => 'SuperAdmin',
            'dui'               => '012345678',
            'email'             => 'superadmin@servidor.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('p@5t15al5abana'),
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
        DB::table('users')->insert([
            'role_id'           => 2,
            'name'              => 'Admin',
            'dui'               => '023456783',
            'email'             => 'admin@servidor.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('p@5t15al5abana'),
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        //ENROLANDO AL ADMINISTRADOR
        try {
            $user = User::findOrFail(1);
            $user->assignRole('SuperAdmin');
        } catch (\Exception $e) {
            echo "Error asignando rol SuperAdmin: " . $e->getMessage();
        }

        try {
            $user = User::findOrFail(2);
            $user->assignRole('Admin');
        } catch (\Exception $e) {
            echo "Error asignando rol Admin: " . $e->getMessage();
        }

        //CREACION DE TAREAS
        DB::table('tareas')->insert([
            'tarea'      => 'Despacho iniciado',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea'      => 'Crear factura digital',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea'      => 'Enviar factura digital',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea'      => 'Descargar Stock',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE SOLICITUDES
        DB::table('solicitudes')->insert([
            'solicitud'  => 'Accesorios para suspensión',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('solicitudes')->insert([
            'solicitud'  => 'Luces adaptativas',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE ESTADOS
        DB::table('estados')->insert([
            'estado'     => 'Recibida',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado'     => 'En progreso',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado'     => 'Resuelta',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado'     => 'Rechazada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado'     => 'Retrasada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado'     => 'Priorizada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    }
}
