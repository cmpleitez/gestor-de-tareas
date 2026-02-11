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
        DB::table('oficinas')->updateOrInsert(
            ['id' => 1],
            [
                'oficina'    => 'Oficina Sede',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //EQUIPOS
        DB::table('equipos')->updateOrInsert(
            ['id' => 1],
            [
                'oficina_id' => 1,
                'equipo'     => 'Personal de atención al cliente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        //CREACION DE PERMISOS
        $permissions = [
            'ver',
            'crear',
            'editar',
            'asignar',
            'eliminar',
            'activar',
            'autorizar',
            'gestionar',
            'administrar',
            'tienda',
            'vaciar_carrito',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //ROLES Y ASIGNACIÓN DE PERMISOS
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // Nota: 'superadmin' se crea SOLO en las tablas de Spatie, no en la tabla 'roles' personalizada
        
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->syncPermissions(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar', 'administrar', 'tienda']);

        $role = Role::firstOrCreate(['name' => 'receptor']);
        $role->syncPermissions(['ver', 'crear', 'editar', 'activar', 'asignar', 'gestionar']);

        $role = Role::firstOrCreate(['name' => 'supervisor']);
        $role->syncPermissions(['ver', 'asignar']);

        $role = Role::firstOrCreate(['name' => 'gestor']);
        $role->syncPermissions(['ver']);

        $role = Role::firstOrCreate(['name' => 'operador']);
        $role->syncPermissions(['ver', 'crear', 'editar', 'asignar']);

        $role = Role::firstOrCreate(['name' => 'cliente']);
        $role->syncPermissions(['ver', 'crear', 'tienda', 'vaciar_carrito']);

        //SUPERADMINISTRADOR
        DB::table('users')->updateOrInsert(
            ['id' => 1],
            [
                'role_id'           => 1,
                'name'              => 'superadmin',
                'dui'               => '012345678',
                'email'             => 'superadmin@servidor.com',
                'email_verified_at' => Carbon::now(),
                'password'          => bcrypt('p@5t15al5abana'),
                'remember_token'    => Str::random(10),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );
        try {
            $user = User::findOrFail(1);
            $user->assignRole('superadmin');
        } catch (\Exception $e) {
            echo "Error asignando rol superadmin: " . $e->getMessage();
        }

        //ADMINISTRADOR
        DB::table('users')->updateOrInsert(
            ['id' => 2],
            [
                'role_id'           => 2,
                'name'              => 'admin',
                'dui'               => '023456783',
                'email'             => 'admin@servidor.com',
                'email_verified_at' => Carbon::now(),
                'password'          => bcrypt('p@5t15al5abana'),
                'remember_token'    => Str::random(10),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        );
        try {
            $user = User::findOrFail(2);
            $user->assignRole('admin');
        } catch (\Exception $e) {
            echo "Error asignando rol admin: " . $e->getMessage();
        }

        //CREACION DE TAREAS
        DB::table('tareas')->updateOrInsert(['id' => 1], ['tarea' => 'Orden de compra en revisión', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 2], ['tarea' => 'Stock físico en revisión', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 3], ['tarea' => 'Pago efectuado', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 4], ['tarea' => 'Stock descargado', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 5], ['tarea' => 'Entrega efectuada', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        //CREACION DE SOLICITUDES
        DB::table('solicitudes')->updateOrInsert(
            ['id' => 1],
            ['solicitud' => 'Orden de compra', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );

        //CREACION DE ESTADOS
        DB::table('estados')->updateOrInsert(['id' => 1], ['estado' => 'Solicitada', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('estados')->updateOrInsert(['id' => 2], ['estado' => 'Recibida', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('estados')->updateOrInsert(['id' => 3], ['estado' => 'En progreso', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('estados')->updateOrInsert(['id' => 4], ['estado' => 'Resuelta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        //CREACION DE PARAMETROS
        DB::table('parametros')->updateOrInsert(['id' => 1], ['parametro' => 'Frecuencia de refresco', 'valor' => '1', 'unidad_medida' => 'minutos', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('parametros')->updateOrInsert(['id' => 2], ['parametro' => 'Nombres de kits automáticos', 'valor' => '1', 'unidad_medida' => 'boolean', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

    }
}
