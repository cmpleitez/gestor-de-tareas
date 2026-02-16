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
    public $detalles;

    public function __construct(Recepcion $recepcion, array $detalles = [])
    {
        $this->recepcion = $recepcion;
        $this->detalles = $detalles;
    }


    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        $ordenId = \App\Services\KeyRipper::rip($this->recepcion->atencion_id);
        return [
            'titulo' => 'Stock Físico Revisado',
            'mensaje' => 'El stock físico de la orden #' . $ordenId . ' ha sido revisado: implica que pueden o no haber items sin stock.',
            'atencion_id' => $this->recepcion->atencion_id,
            'detalles' => $this->detalles,
        ];

    }
}
