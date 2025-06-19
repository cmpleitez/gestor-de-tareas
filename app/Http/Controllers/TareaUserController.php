<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TareaUser;
use App\Models\Solicitud;

class TareaUserController extends Controller
{
    public function index()
    {
        $tareas_users = TareaUser::orWhere('user_id_origen', auth()->user()->id)->orWhere('user_id_destino', auth()->user()->id)->get();
        return view('modelos.TareaUser.index', compact( 'envios'));
    }
    public function create()
    {
        $solicitudes = Solicitud::where('activo', true)->get();
        return view('modelos.TareaUser.create', compact('solicitudes'));
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
