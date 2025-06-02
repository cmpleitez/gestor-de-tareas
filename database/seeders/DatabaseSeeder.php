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
use App\Models\Zona;

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

        //USUARIOS
        DB::table('users')->insert([
            'area_id'           => 2,
            'name'              => 'hari.seldom.sv@gmail.com',
            'email'             => 'hari.seldom.sv@gmail.com',
            'email_verified_at' => null,
            'password'          => bcrypt('123456789'),
            'remember_token'    => Str::random(10),
            'profile_photo_path' => 'profile-photos/superadmin.png',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        //EQUIPOS
        DB::table('equipos')->insert([
            'equipo'              => 'Equipo de desarrollo de sistemas informáticos y aplicaciones web',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
        DB::table('equipos')->insert([
            'equipo'              => 'Equipo de innovación',
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
        $role = Role::create(['name' => 'Administradores', 'icon' => 'bx bxs-briefcase-alt-2']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar']);
        $role = Role::create(['name' => 'Gestores de cumplimiento', 'icon' => 'bx bxs-star-half']);
        $role->givePermissionTo(['ver']);
        $user = User::findOrFail(1);
        $user->assignRole('SuperAdmin');

        //ENROLANDO AL ADMINISTRADOR
        $user = User::findOrFail(1);
        $user->assignRole('SuperAdmin');
    }
}

