<?php
namespace App\Http\Middleware;

use App\Models\Recepcion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerificarRecepcionPropia
{
    /**
     * Autorización por objeto: los permisos Spatie responden "¿puede hacer esta clase de acción?",
     * este middleware responde "¿esta solicitud es suya?". Sin él, un participante del flujo puede
     * operar la recepción de otro (incluso de otra oficina) enviando un id ajeno.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario      = auth()->user();
        $recepcion_id = $this->recepcionIdSolicitado($request);
        $atencion_id  = $request->input('atencion_id');
        if (!$recepcion_id && !$atencion_id) { //Endpoints que no operan sobre una recepción concreta (catálogos, stock, reportes)
            return $next($request);
        }
        if ($recepcion_id) {
            $recepcion = Recepcion::with('atencion')->find($recepcion_id);
            if (!$recepcion) {
                return $this->denegar($request, 'La solicitud indicada no existe.', 404);
            }
            $participante = in_array($usuario->id, [$recepcion->origen_user_id, $recepcion->destino_user_id], true);
            $misma_oficina = optional($recepcion->atencion)->oficina_id === $usuario->oficina_id;
            if (!$participante || !$misma_oficina) {
                return $this->denegar($request, 'No participa en esta solicitud.');
            }
            if ($atencion_id && $recepcion->atencion_id !== $atencion_id) { //El par recepción/atención debe ser coherente
                return $this->denegar($request, 'La solicitud no corresponde a la atención indicada.');
            }
            return $next($request);
        }
        $participa = Recepcion::where('atencion_id', $atencion_id) //Solo llega atencion_id (ej. editarCarrito): basta participar en alguna de sus recepciones
            ->where(function ($query) use ($usuario) {
                $query->where('origen_user_id', $usuario->id)
                    ->orWhere('destino_user_id', $usuario->id);
            })
            ->whereHas('atencion', function ($query) use ($usuario) {
                $query->where('oficina_id', $usuario->oficina_id);
            })
            ->exists();
        if (!$participa) {
            return $this->denegar($request, 'No participa en esta solicitud.');
        }
        return $next($request);
    }

    private function recepcionIdSolicitado(Request $request): ?string
    {
        $desde_ruta = $request->route('recepcion') ?? $request->route('recepcion_id'); //Puede venir ya resuelto como modelo por SubstituteBindings
        if ($desde_ruta instanceof Recepcion) {
            return $desde_ruta->id;
        }
        return $desde_ruta ?? $request->input('recepcion_id');
    }

    private function denegar(Request $request, string $mensaje, int $codigo = 403): Response
    {
        Log::warning('Log:: [Usuario: ' . auth()->user()->name . '] Acceso denegado a solicitud ajena: ' . $mensaje, [
            'ruta'         => $request->path(),
            'recepcion_id' => $this->recepcionIdSolicitado($request),
            'atencion_id'  => $request->input('atencion_id'),
        ]);
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $mensaje,
                'type'    => 'error'
            ], $codigo);
        }
        abort($codigo, $mensaje);
    }
}
