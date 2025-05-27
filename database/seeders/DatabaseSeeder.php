<?php
namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //USUARIOS
        DB::table('users')->insert([
            'name'              => 'hari.seldom.sv@gmail.com',
            'email'             => 'hari.seldom.sv@gmail.com',
            'email_verified_at' => null,
            'password'          => bcrypt('123456789'),
            'remember_token'    => Str::random(10),
            'profile_photo_path' => 'profile-photos/superadmin.png',
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
        $role = Role::create(['name' => 'SuperAdmin']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);
        $role = Role::create(['name' => 'Administradores']);
        $role->givePermissionTo(['ver', 'crear', 'editar', 'activar', 'eliminar', 'autorizar']);
        $role = Role::create(['name' => 'Gestores de cumplimiento']);
        $role->givePermissionTo(['ver']);
        $user = User::findOrFail(1);
        $user->assignRole('SuperAdmin');
    }
}
