<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Recepcion;

class StockRevisadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $recepcion;

    public function __construct(Recepcion $recepcion)
    {
        $this->recepcion = $recepcion;
    }

    public function via(object $notifiable): array
    {
        // Por ahora enviamos por base de datos y mail para asegurar visibilidad
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ordenId = \App\Services\KeyRipper::rip($this->recepcion->atencion_id);
        return (new MailMessage)
            ->subject('Stock Revisado: Orden #' . $ordenId)
            ->line('El stock físico ha sido revisado: implica que pueden o no haber items sin stock.')
            ->action('Ver Solicitudes', route('recepcion.solicitudes'));
    }

    public function toArray(object $notifiable): array
    {
        $ordenId = \App\Services\KeyRipper::rip($this->recepcion->atencion_id);
        return [
            'titulo' => 'Stock Físico Revisado',
            'mensaje' => 'El stock físico de la orden #' . $ordenId . ' ha sido revisado: implica que pueden o no haber items sin stock.',
            'atencion_id' => $this->recepcion->atencion_id,
        ];
    }
}
