<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TareaUser;

class TareaUserController extends Controller
{
    public function index()
    {
        $envios = TareaUser::orWhere('user_id_origen', auth()->user()->id)->orWhere('user_id_destino', auth()->user()->id)->get();
        return view('modelos.TareaUser.index', compact( 'envios'));
    }
    public function create()
    {
        return view('modelos.TareaUser.create');
    }

    public function store(Request $request)
    {
        return $request->all();
    }



    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function show(string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
