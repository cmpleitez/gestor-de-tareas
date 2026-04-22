<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Recepcion;
use App\Services\KeyRipper;

class CarritoRevisadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recepcion;

    public function __construct(Recepcion $recepcion)
    {
        $this->recepcion = $recepcion;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $ordenId = KeyRipper::rip($this->recepcion->atencion_id);
        return [
            'titulo'      => 'Carrito Revisado - Re-confirmar Stock',
            'mensaje'     => 'La solicitud #' . $ordenId . ' ha sido revisada por el receptor. El stock debe ser re-confirmado.',
            'atencion_id' => $this->recepcion->atencion_id,
            'tipo'        => 'warning',
        ];
    }
}
