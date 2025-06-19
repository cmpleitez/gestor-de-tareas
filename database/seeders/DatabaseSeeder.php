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

        //AREAS
        DB::table('areas')->insert([
            'area' => 'Innovación',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);        
        DB::table('areas')->insert([
            'area' => 'Desarrollo de sistemas informáticos y aplicaciones web',
            'zona_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //OFICINAS
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina Sede',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 1',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 2',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 3',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 4',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 5',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 6',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 7',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([
            'oficina' => 'Oficina - Zona 8',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([ 
            'oficina' => 'Oficina - Zona 9',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([ 
            'oficina' => 'Oficina - Zona 10',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([     
            'oficina' => 'Oficina - Zona 11',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]); 
        DB::table('oficinas')->insert([     
            'oficina' => 'Oficina - Zona 12',
            'area_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //USUARIOS
        DB::table('users')->insert([
            'oficina_id'           => 1,
            'name'              => 'Hari Seldom',
            'dui'               => '012345678',
            'email'             => 'hari.seldom.sv@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('123456789'),
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        //EQUIPOS
        DB::table('equipos')->insert([
            'equipo'              => 'Desarrollo de sistemas informáticos y aplicaciones web',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
        DB::table('equipos')->insert([
            'equipo'              => 'Innovación',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        //CREACION DE PERMISOS
        $permissions = [
            'ver',
            'crear',
            'editar',
            'eliminar',
            'activar',
            'autorizar',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //ROLES Y ASIGNACIÓN DE PERMISOS
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $role = Role::create(['name' => 'SuperAdmin', 'icon' => 'bx bxs-shield-alt-2']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);
        $role = Role::create(['name' => 'Administrador', 'icon' => 'bx bxs-briefcase-alt-2']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar']);
        $role = Role::create(['name' => 'Supervisor', 'icon' => 'bx bxs-star-half']);
        $role->givePermissionTo(['ver']);
        $role = Role::create(['name' => 'Operador', 'icon' => 'bx bxs-group']);
        $role->givePermissionTo(['ver', 'crear', 'editar']);
        $role = Role::create(['name' => 'Beneficiario', 'icon' => 'bx bxs-user']);
        $role->givePermissionTo(['ver', 'crear', 'editar']);
        $user = User::findOrFail(1);
        $user->assignRole('SuperAdmin');

        //ENROLANDO AL ADMINISTRADOR
        $user = User::findOrFail(1);
        $user->assignRole('SuperAdmin');

        //CREACION DE TAREAS
        DB::table('tareas')->insert([
            'tarea' => 'Sopesar la cantidad y el nivel de necesidad de las solicitudes versus la cantidad de población de la zona',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Inspección de campo para evaluar la viabilidad de la obra',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Planeación y diseño de la obra',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Autorización de la obra y reservación de dinero del presupuesto municipal',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Inicio de los trabajos',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Supervisión de la calidad de los trabajos, verificación de comprobantes y facturas de gastos versus los avances de la obra',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('tareas')->insert([
            'tarea' => 'Finalización y entrega pública de la obra',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        //CREACION DE SOLICITUDES
        DB::table('solicitudes')->insert([
            'solicitud' => 'Creación y mantenimiento de espacio público',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('solicitudes')->insert([
            'solicitud' => 'Reparación de espacio público',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('solicitudes')->insert([
            'solicitud' => 'Construcción de muro de contención',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('solicitudes')->insert([
            'solicitud' => 'Construcción de red de vigilancia para la seguridad civil',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    }
}

