<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Recepcion;
use App\Models\Solicitud;
use App\Models\Oficina;
use App\Models\User;

class RecepcionController extends Controller
{

    public function index()
    {
        $recepciones = Recepcion::orWhere('user_id_origen', auth()->user()->id)->orWhere('user_id_destino', auth()->user()->id)->get();
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

        $maxCorrelativo = Recepcion::max('id');
        $maxCorrelativo = $maxCorrelativo ? substr($maxCorrelativo, -5) : null;
        $correlativo = $maxCorrelativo ? str_pad($maxCorrelativo + 1, 5, '0', STR_PAD_LEFT) : '00001';
        $anio_actual = date('Y');
        $id = $anio_actual . $correlativo;

        if ($recepcionistas->isEmpty()) {
            return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
        }
        $recepcionista = $recepcionistas->random();
        try {
            $recepcion = new Recepcion([
                'id' => $id,
                'user_id_origen' => auth()->user()->id, //beneficiario
                'user_id_destino' => $recepcionista->id, //Recepcionista
                'solicitud_id' => $request->solicitud_id,
                'oficina_id' => auth()->user()->oficina_id,
                'detalles' => $request->detalles,
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
