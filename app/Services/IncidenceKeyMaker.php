<?php

namespace App\Services;

class IncidenceKeyMaker
{
    /**
     * Genera un identificador único de 14 dígitos para una incidencia.
     * La clave se compone del identificador de la actividad (12 dígitos)
     * seguido de un correlativo de incidencia de 2 dígitos.
     *
     * @param string $actividad_id Id de la actividad (12 dígitos) a la que pertenece la incidencia.
     * @return string Identificador único de la incidencia.
     *
     * @throws \Exception Si el id de la actividad no tiene 12 dígitos.
     */
    public function generate(string $actividad_id): string
    {
        // Validar que el id de la actividad tenga exactamente 12 dígitos
        if (strlen($actividad_id) !== 12 || !ctype_digit($actividad_id)) {
            throw new \Exception('El id de la actividad debe contener exactamente 12 dígitos numéricos');
        }

        $modelClass = 'App\\Models\\Incidencia';

        // Buscar el máximo id que coincida con el prefijo de la actividad
        $maxId = $modelClass::whereNotNull('id')
            ->where('id', 'LIKE', $actividad_id . '%')
            ->max('id');

        if ($maxId) {
            // Extraer los últimos 2 dígitos (correlativo de incidencia)
            $correlativo_incidencia_actual = (int) substr($maxId, -2);
            $nuevo_correlativo_incidencia = str_pad($correlativo_incidencia_actual + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $nuevo_correlativo_incidencia = '01'; // Primer correlativo de incidencia para la actividad
        }

        return $actividad_id . $nuevo_correlativo_incidencia;
    }
} 