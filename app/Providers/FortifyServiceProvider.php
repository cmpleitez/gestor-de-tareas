<?php
namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Debe llamarse en register(): el provider de vendor registra sus rutas
        // en boot() y se ejecuta antes que este provider, por lo que hacerlo
        // en boot() no evita las rutas duplicadas de Fortify.
        Fortify::ignoreRoutes();
    }

    public function boot(): void
    {
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Solo usuarios activos pueden iniciar sesión (users.activo)
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', Str::lower($request->input(Fortify::username())))->first();

            if ($user && $user->activo && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        // Registrar rutas de Fortify manualmente
        $this->registerFortifyRoutes();
    }

    /**
     * Registrar las rutas de Fortify manualmente con middleware personalizado
     */
    protected function registerFortifyRoutes(): void
    {
        // Rutas de autenticación (accesibles sin autenticación)
        Route::group(['middleware' => ['web']], function () {
            // Login
            Route::get('/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'create'])
                ->middleware(['guest'])
                ->name('login');

            // throttle:login es obligatorio: al haber un limiter configurado,
            // Fortify omite EnsureLoginIsNotThrottled de su pipeline y delega
            // el rate limiting a este middleware de ruta.
            Route::post('/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store'])
                ->middleware(['guest', 'throttle:login']);

            // Logout
            Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

            // Desafío de dos factores (usuarios con 2FA activado)
            Route::get('/two-factor-challenge', [\Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest'])
                ->name('two-factor.login');

            Route::post('/two-factor-challenge', [\Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController::class, 'store'])
                ->middleware(['guest', 'throttle:two-factor'])
                ->name('two-factor.login.store');

            // Password Reset
            Route::get('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'create'])
                ->middleware(['guest'])
                ->name('password.request');

            Route::post('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'store'])
                ->middleware(['guest'])
                ->name('password.email');

            Route::get('/reset-password/{token}', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'create'])
                ->middleware(['guest'])
                ->name('password.reset');

            Route::post('/reset-password', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'store'])
                ->middleware(['guest'])
                ->name('password.update');

            // Email Verification: las rutas viven en routes/web.php (verification.notice/verify/send)
        });

        // Rutas de registro: solo usuarios autenticados con rol admin
        Route::group(['middleware' => ['web', 'auth', 'role:admin']], function () {
            Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'create'])
                ->name('register');

            Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store']);
        });
    }
}
