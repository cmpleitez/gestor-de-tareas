<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Recepcion;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\IdGenerator;

class RecepcionController extends Controller
{

    public function index()
    {
        $recepciones = Recepcion::orWhere('user_id_origen', auth()->user()->id)
        ->orWhere('user_id_destino', auth()->user()->id)
        ->with('solicitud')->get();
        return view('modelos.recepcion.index', compact( 'recepciones'));
    }


    public function create()
    {
        $solicitudes = Solicitud::where('activo', true)->get();
        return view('modelos.recepcion.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        $recepcionistas = User::role('Recepcionista')->where('oficina_id', auth()->user()->oficina_id)->get();
        if ($recepcionistas->isEmpty()) {
            return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
        }
        $recepcionista = $recepcionistas->random();
        try {
            $id = (new IdGenerator())->generate();
            $recepcion = new Recepcion([
                'id' => $id,
                'user_id_origen' => auth()->user()->id, //beneficiario
                'user_id_destino' => $recepcionista->id, //Recepcionista
                'solicitud_id' => $request->solicitud_id,
                'oficina_id' => auth()->user()->oficina_id, //Oficina destino
                'detalles' => $request->detalles,
                'activo' => false, //Por defecto invalidada, se valida al recibir la solicitud
            ]);
            $recepcion->save();
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba enviar la solicitud' . $e->getMessage());
        }
        return redirect()->route('recepcion')->with('success', 'La solicitud número "' . $id . '" ha sido recibida en ' . auth()->user()->oficina->oficina);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function activate(Recepcion $recepcion)
    {
        $recepcion->activo = !$recepcion->activo;
        $recepcion->save();
        return redirect()->route('recepcion')->with('success', 'La solicitud "' . $recepcion->solicitud->solicitud . '" ha sido ' . ($recepcion->activo ? 'validada' : 'invalidada'));
    }


}
