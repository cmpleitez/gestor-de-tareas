<?php
namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //DISTRITOS
        DB::table('distritos')->insert([
            'distrito' => 'San Salvador',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('distritos')->insert([
            'distrito' => 'Ayutuxtepeque',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('distritos')->insert([
            'distrito' => 'Cuscatancingo',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('distritos')->insert([
            'distrito' => 'Ciudad delgado',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);        
        DB::table('distritos')->insert([
            'distrito' => 'Mejicanos',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //ZONAS
        DB::table('zonas')->insert([
            'zona' => 'San Salvador Sede',
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);         
        DB::table('zonas')->insert([
            'zona' => 'San Salvador zona uno',
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'San Salvador zona dos',
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'San Salvador zona tres',
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'San Salvador zona cuatro',
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'San Salvador zona cinco',    
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'San Salvador zona seis', 
            'distrito_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'Mejicanos zona siete',   
            'distrito_id' => 5,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'Ayutuxtepeque zona ocho',
            'distrito_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'Ayutuxtepeque zona nueve',
            'distrito_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'Custatancingo zona diez',    
            'distrito_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'Cuscatancingo zona once', 
            'distrito_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('zonas')->insert([
            'zona' => 'Ciudad delgado zona doce',  
            'distrito_id' => 4,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //OFICINAS
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina Sede',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 1',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 2',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 3',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 4',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 5',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 6',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 7',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 8',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([ 
            'oficina' => 'Oficina - Zona 9',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([ 
            'oficina' => 'Oficina - Zona 10',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([     
            'oficina' => 'Oficina - Zona 11',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([     
            'oficina' => 'Oficina - Zona 12',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //AREAS
        DB::table('areas')->insert([
            'area' => 'Ventas',
            'oficina_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //EQUIPOS
        DB::table('equipos')->insert([
            'equipo'              => 'Técnicos en suspensión',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
        DB::table('equipos')->insert([
            'equipo'              => 'Técnicos en luces adaptativas',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        //CREACION DE PERMISOS
        $permissions = [
            'ver',
            'crear',
            'editar',
            'derivar',
            'asignar',
            'delegar',
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
        
        $role = Role::create(['name' => 'Administrador']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar']);
        
        $role = Role::create(['name' => 'Recepcionista']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'derivar']);

        $role = Role::create(['name' => 'Supervisor']);
        $role->givePermissionTo(['ver', 'asignar']);

        $role = Role::create(['name' => 'Gestor']);
        $role->givePermissionTo(['ver', 'delegar']);
        
        $role = Role::create(['name' => 'Operador']);
        $role->givePermissionTo(['ver', 'crear', 'editar']);

        $role = Role::create(['name' => 'Beneficiario']);
        $role->givePermissionTo(['ver', 'crear', 'editar']);

        //USUARIOS
        DB::table('users')->insert([
            'area_id'           => 1,
            'role_id'           => 1,
            'name'              => 'Superadmin',
            'dui'               => '012345678',
            'email'             => 'cpleitez.2024@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('p@5t15al5abana'),
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        //ENROLANDO AL ADMINISTRADOR
        $user = User::findOrFail(1);
        $user->assignRole('SuperAdmin');

        //CREACION DE TAREAS
        DB::table('tareas')->insert([
            'tarea' => 'Revisión de viabilidad técnica',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Revisión de stock',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE SOLICITUDES
        DB::table('solicitudes')->insert([
            'solicitud' => 'Instalación de amortiguadores',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('solicitudes')->insert([
            'solicitud' => 'Instalación de luces adaptativas',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE ESTADOS
        DB::table('estados')->insert([
            'estado' => 'Recibida',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado' => 'En progreso',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado' => 'Resuelta',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado' => 'Rechazada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado' => 'Retrasada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('estados')->insert([
            'estado' => 'Priorizada',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    }
}

