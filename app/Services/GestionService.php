<?php

namespace App\Services;

use App\Models\Recepcion;
use App\Models\Actividad;
use App\Models\Estado;
use Carbon\Carbon;

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
            $traza = 'Solicitada';
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
}
