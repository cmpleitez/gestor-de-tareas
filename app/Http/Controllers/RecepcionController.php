<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Recepcion;

class RecepcionController extends Controller
{

    public function index()
    {
        $recepciones = Recepcion::orWhere('user_id_origen', auth()->user()->id)->orWhere('user_id_destino', auth()->user()->id)->get();
        return view('modelos.Recepcion.index', compact( 'recepciones'));
    }


    public function create()
    {
        $solicitudes = Solicitud::where('activo', true)->get();
        $oficinas = Oficina::where('activo', true)->get();
        return view('modelos.TareaUser.create', compact('solicitudes', 'oficinas'));
    }


    public function store(Request $request)
    {
        $recepcionistas = User::role('Recepcionista')->where('oficina_id', $request->oficina_id)->get();
        $recepcionista = $recepcionistas->random();
        if ($recepcionista) {
            return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
        }
        
        //registrar el envio

        try {
            $recepcion = Recepcion::create([
                'user_id_origen' => auth()->user()->id, //beneficiario
                'user_id_destino' => $recepcionista->id, //Recepcionista
                'solicitud_id' => $request->solicitud_id,
                'oficina_id' => auth()->user()->oficina_id,
                'detalles' => $request->detalles,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba enviar la solicitud' . $e->getMessage());
        }
        return redirect()->route('envio')->with('success', 'La solicitud número "' . $recepcion->id . '" ha sido recibida');
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
