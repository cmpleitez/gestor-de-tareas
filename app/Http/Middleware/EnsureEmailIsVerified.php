<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tu correo electrónico no está verificado.'], 409);
            }

            // Si la ruta actual es la de verificación de correo, permitir el acceso
            if ($request->routeIs('verification.notice') || $request->routeIs('verification.verify') || $request->routeIs('verification.send')) {
                return $next($request);
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
} 