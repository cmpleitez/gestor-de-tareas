<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Recepcion;

class OrdenRevisadaNotification extends Notification implements ShouldQueue
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
        $ordenId = \App\Services\KeyRipper::rip($this->recepcion->atencion_id);
        return [
            'titulo' => 'Orden Revisada',
            'mensaje' => 'La orden #' . $ordenId . ' ha sido revisada, por favor abra su correo '.$notifiable->email.' y valídela para continuar con la gestión del pago y envío respectivos.',
            'atencion_id' => $this->recepcion->atencion_id,
        ];
    }
}
