<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (UnauthorizedException $e) {
            $user = auth()->user();
            Log::error(
                'Log:: [Usuario: ' . ($user?->name ?? 'no autenticado') . '] Acceso denegado (403) a: ' . request()->fullUrl(),
                ['roles' => $user?->getRoleNames(), 'exception' => $e]
            );
            return response()->view('errors.403', [], 403);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
