<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('modelos.user.index', compact('users'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function rolesEdit(User $user)
    {
        $roles = Role::where('name', '!=', 'SuperAdmin')->get();
        return view('modelos.user.roles-edit', ['user' => $user, 'roles' => $roles]);
    }

    public function update(Request $request, User $user)
    {
        return 'update';
    }

    public function destroy(string $id)
    {
        //
    }
}
