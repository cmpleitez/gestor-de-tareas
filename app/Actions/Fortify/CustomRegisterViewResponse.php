<?php
namespace App\Actions\Fortify;
use Laravel\Fortify\Contracts\RegisterViewResponse;

use App\Models\Oficina;
class CustomRegisterViewResponse implements RegisterViewResponse
{
    public function toResponse($request)
    {
        $oficinas = Oficina::where('activo', true)->get();
        return response(view('auth.register', [
            'oficinas' => $oficinas,
        ]));
    }
} 