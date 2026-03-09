<?php
namespace App\Services;

use App\Models\Recepcion;
use App\Models\Actividad;
use App\Models\Estado;
use App\Models\Atencion;


use Illuminate\Support\Facades\Log;



class GestionService
{
    public function obtenerUsuariosParticipantes($atencionIds)
    {
        $usuariosDestino = Recepcion::with(['usuarioDestino', 'role'])
            ->whereIn('atencion_id', $atencionIds)
            ->get()
            ->map(function ($recepcion) {
                return [
                    'recepcion_id'        => $recepcion->id,
                    'atencion_id'         => $recepcion->atencion_id,
                    'name'                => $recepcion->usuarioDestino->name,
                    'profile_photo_url'   => $recepcion->usuarioDestino->profile_photo_url,
                    'recepcion_role_name' => $recepcion->role->name,
                    'tipo'                => 'destino',
                ];
            });

        $usuariosOrigen = Recepcion::with(['usuarioOrigen', 'role'])
            ->whereIn('atencion_id', $atencionIds)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Receptor');
            })
            ->get()
            ->map(function ($recepcion) {
                return [
                    'recepcion_id'        => $recepcion->id,
                    'atencion_id'         => $recepcion->atencion_id,
                    'name'                => $recepcion->usuarioOrigen->name,
                    'profile_photo_url'   => $recepcion->usuarioOrigen->profile_photo_url,
                    'recepcion_role_name' => $recepcion->usuarioOrigen->mainRole->name,
                    'tipo'                => 'origen',
                ];
            });

        return $usuariosDestino->merge($usuariosOrigen)
            ->groupBy('atencion_id')
            ->map(function ($grupo) {
                return $grupo->unique(function ($usuario) {
                    return $usuario['recepcion_id'] . '_' . $usuario['tipo'];
                })->values();
            });
    }

    public function obtenerTraza($tarjeta)
    {
        $estado_resuelta_id = Estado::where('estado', 'Resuelta')->first()->id;
        $actividades = Actividad::whereHas('recepcion', function ($query) use ($tarjeta) {
            $query->where('atencion_id', $tarjeta->atencion_id);
        })
        ->with(['tarea', 'estado'])
        ->get()
        ->sortByDesc('updated_at');

        if ($actividades->isEmpty()) {
            $recepcion = $tarjeta instanceof Recepcion ? $tarjeta : Recepcion::find($tarjeta->recepcion_id);
            $traza = optional($recepcion->solicitud->tareas->first())->tarea ?? 'Solicitud';
        } else {
            $todasResueltas = $actividades->every(function ($actividad) use ($estado_resuelta_id) {
                return $actividad->estado_id == $estado_resuelta_id;
            });

            if ($todasResueltas) {
                $traza = 'Resuelta';
            } else {
                $ultimaActividad = $actividades->first();
                $traza           = $ultimaActividad->tarea->tarea;
            }
        }
        return $traza;
    }

    public function reportarTarea($nombre_tarea, $recepcion_id, $atencion_id)
    {
        $estado_resuelta_id = Estado::where('estado', 'Resuelta')->first()->id;
        $actividad = Actividad::where('recepcion_id', $recepcion_id)
            ->whereHas('tarea', function($q) use ($nombre_tarea) {
                $q->where('tarea', $nombre_tarea);
            })
            ->first();
        if ($actividad) {
            $actividad->estado_id = $estado_resuelta_id;
            $actividad->save();
            $total_actividades_globales = Actividad::whereHas('recepcion', function($query) use ($atencion_id) {
                $query->where('atencion_id', $atencion_id);
            })->count();
            $actividades_resueltas_globales = Actividad::whereHas('recepcion', function($query) use ($atencion_id) {
                $query->where('atencion_id', $atencion_id);
            })->where('estado_id', $estado_resuelta_id)
            ->count();

            

Log::info("total_actividades_globales ".$total_actividades_globales." | actividades_resueltas_globales ".$actividades_resueltas_globales);


            $porcentaje_avance = $total_actividades_globales > 0 
                ? round(($actividades_resueltas_globales / $total_actividades_globales) * 100, 2) 
                : 0;
            $atencion = Atencion::find($atencion_id);
            if ($atencion) {
                $atencion->avance = $porcentaje_avance;
                $atencion->save();
            }
            if ($porcentaje_avance >= 100) {
                Recepcion::where('atencion_id', $atencion_id)
                    ->update(['estado_id' => $estado_resuelta_id]);
                $atencion->estado_id = $estado_resuelta_id;
                $atencion->save();
            }
        }
    }
}
