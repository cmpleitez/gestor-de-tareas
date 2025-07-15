<?php
namespace App\Actions\Fortify;
use Laravel\Fortify\Contracts\RegisterViewResponse;

use App\Models\Area;
class CustomRegisterViewResponse implements RegisterViewResponse
{
    public function toResponse($request)
    {
        $areas = Area::where('activo', true)->get();
        return response(view('auth.register', [
            'areas' => $areas,
        ]));
    }
}