<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CheckNotifications extends Component
{
    public function checkNotifications()
    {
        $user = Auth::user();
        if ($user) {
            $unreadNotifications = $user->unreadNotifications;

            foreach ($unreadNotifications as $notification) {
                $this->dispatch('notification-received',
                    titulo: $notification->data['titulo'] ?? 'Notificación',
                    mensaje: $notification->data['mensaje'] ?? '',
                    tipo: 'info',
                    payload: $notification->data
                );
                $notification->markAsRead();
            }

        }
    }

    public function render()
    {
        return view('livewire.check-notifications');
    }
}
