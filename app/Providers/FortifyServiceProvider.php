<?php
namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

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

        Fortify::registerView(function () {
            // Permitir acceso al formulario de registro incluso si el usuario está autenticado
            return view('auth.register');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });

        // Configurar redirecciones personalizadas
        Fortify::redirects('register', function () {
            return redirect('/email/verify')->with('success', 'Usuario registrado exitosamente. Por favor verifica tu email.');
        });

        // Permitir que usuarios autenticados accedan al formulario de registro
        Fortify::ignoreRoutes();

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

            Route::post('/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store'])
                ->middleware(['guest']);

            // Logout
            Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

            // Password Reset (solo para SuperAdmin)
            Route::get('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'create'])
                ->middleware(['role:SuperAdmin'])
                ->name('password.request');

            Route::post('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'store'])
                ->middleware(['role:SuperAdmin'])
                ->name('password.email');

            Route::get('/reset-password/{token}', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'create'])
                ->middleware(['role:SuperAdmin'])
                ->name('password.reset');

            Route::post('/reset-password', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'store'])
                ->middleware(['role:SuperAdmin'])
                ->name('password.update');

            // Email Verification
            Route::get('/email/verify', [\Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::class, '__invoke'])
                ->middleware(['auth'])
                ->name('verification.notice');

            Route::get('/email/verify/{id}/{hash}', [\Laravel\Fortify\Http\Controllers\VerifyEmailController::class, '__invoke'])
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

            Route::post('/email/verification-notification', [\Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');
        });

        // Rutas de registro (protegidas por rol SuperAdmin)
        Route::group(['middleware' => ['web', 'role:SuperAdmin']], function () {
            Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'create'])
                ->name('register');

            Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store']);
        });
    }
}
