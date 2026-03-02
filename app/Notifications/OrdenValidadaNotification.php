<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Recepcion;

class OrdenValidadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recepcion;

    public function __construct(Recepcion $recepcion)
    {
        $this->recepcion = $recepcion;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ordenId = \App\Services\KeyRipper::rip($this->recepcion->atencion_id);
        return (new MailMessage)
            ->subject('Orden #' . $ordenId . ' Validada')
            ->markdown('mail.orden-validada', ['recepcion' => $this->recepcion]);
    }


    public function toArray(object $notifiable): array
    {
        $ordenId = \App\Services\KeyRipper::rip($this->recepcion->atencion_id);
        return [
            'titulo' => 'Orden Validada',
            'mensaje' => 'La orden #' . $ordenId . ' ha sido validada, por favor abra su correo '.$notifiable->email.' y proceda con la gestión respectiva.',
            'atencion_id' => $this->recepcion->atencion_id,
        ];
    }
}
