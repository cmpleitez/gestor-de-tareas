<?php
namespace Database\Seeders;

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
                'oficina'    => 'Mostro',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        DB::table('oficinas')->updateOrInsert(
            ['id' => 2],
            [
                'oficina'    => 'Dodinsons',
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
            'tienda',
            'gestionar',            
            'administrar',
            'ver',
            'crear',
            'editar',
            'asignar',
            'eliminar',
            'activar',
            'autorizar',
            'revisar',
            'confirmar',
            'autorefrescar',
            'ver-solicitudes',
            'ver-tareas'
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //ROLES Y ASIGNACIÓN DE PERMISOS
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $role = Role::firstOrCreate(['name' => 'superadmin']);
        
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->syncPermissions(['administrar', 'ver', 'crear', 'editar', 'activar', 'eliminar', 'asignar', 'autorizar']);

        $role = Role::firstOrCreate(['name' => 'receptor']);
        $role->syncPermissions(['gestionar', 'tienda', 'ver', 'crear', 'editar', 'eliminar', 'revisar', 'asignar', 'autorefrescar', 'ver-solicitudes', 'ver-tareas']);

        $role = Role::firstOrCreate(['name' => 'operador']);
        $role->syncPermissions(['gestionar', 'tienda', 'editar', 'autorefrescar', 'asignar', 'confirmar', 'ver-solicitudes', 'ver-tareas']);

        $role = Role::firstOrCreate(['name' => 'cliente']);
        $role->syncPermissions(['tienda', 'ver', 'crear' ,'ver-solicitudes', 'eliminar']);

        $role = Role::firstOrCreate(['name' => 'supervisor']);

        $role->syncPermissions(['ver']);

        $role = Role::firstOrCreate(['name' => 'gestor']);
        $role->syncPermissions(['ver']);


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
        try {
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
            $user = User::findOrFail(2);
            $user->assignRole('admin');
        } catch (\Exception $e) {
            echo "Error asignando rol admin: " . $e->getMessage();
        }

        //CREACION DE TAREAS
        DB::table('tareas')->updateOrInsert(['id' => 1], ['tarea' => 'Revisión', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 2], ['tarea' => 'Confirmación', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 3], ['tarea' => 'Pago', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 4], ['tarea' => 'Descarga', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('tareas')->updateOrInsert(['id' => 5], ['tarea' => 'Entrega', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        //CREACION DE SOLICITUDES
        DB::table('solicitudes')->updateOrInsert(
            ['id' => 1],
            ['solicitud' => 'Orden de compra', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );

        //CREACION DE ESTADOS
        DB::table('estados')->updateOrInsert(['id' => 1], ['estado' => 'Recibida', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('estados')->updateOrInsert(['id' => 2], ['estado' => 'En progreso', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('estados')->updateOrInsert(['id' => 3], ['estado' => 'Resuelta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        //CREACION DE PARAMETROS
        DB::table('parametros')->updateOrInsert(['id' => 1], ['parametro' => 'Frecuencia de refresco', 'valor' => '60', 'unidad_medida' => 'segundos', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('parametros')->updateOrInsert(['id' => 2], ['parametro' => 'Nombres de kits automáticos', 'valor' => '0', 'unidad_medida' => 'boolean', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        DB::table('parametros')->updateOrInsert(['id' => 3], ['parametro' => 'Uso interno', 'valor' => '1', 'unidad_medida' => 'boolean', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
