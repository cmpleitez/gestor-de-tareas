<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\equipoController;
use App\Http\Controllers\userController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rutas de verificación de correo
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('success', '¡Correo electrónico verificado exitosamente!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', '¡Enlace de verificación reenviado!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Rutas protegidas que requieren verificación de correo
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //ADMINISTRADORES
    Route::group(['middleware' => ['role:Administradores']], function () {
        Route::group(['prefix' => 'equipo'], function () {
            Route::get('/', [equipoController::class, 'index'])->name('equipo');
        });
    });
    
    //SUPER ADMINISTRADORES
    Route::group(['middleware' => ['role:SuperAdmin']], function () {
        Route::group(['prefix' => 'user'], function () { //Usuarios
            Route::get('/', [userController::class, 'index'])->name('user');
            Route::get('create', [userController::class, 'create'])->name('user.create');
            Route::post('store', [userController::class, 'store'])->name('user.store');
            Route::get('edit/{user}', [userController::class, 'edit'])->name('user.edit');
            Route::put('update/{user}', [userController::class, 'update'])->name('user.update');
            Route::get('roles-edit/{user}', [userController::class, 'rolesEdit'])->name('user.roles-edit');
            Route::post('roles-update/{user}', [userController::class, 'rolesUpdate'])->name('user.roles-update');
            Route::get('destroy/{user}', [userController::class, 'destroy'])->name('user.destroy');
        });

        Route::group(['prefix' => 'equipo'], function () { //Equipos
            Route::get('/', [equipoController::class, 'index'])->name('equipo');
            Route::post('activate/{equipo}', [equipoController::class, 'activate'])->name('equipo.activate');
            Route::get('create', [equipoController::class, 'create'])->name('equipo.create');
            Route::post('store', [equipoController::class, 'store'])->name('equipo.store');
            Route::get('edit/{equipo}', [equipoController::class, 'edit'])->name('equipo.edit');
            Route::put('update/{equipo}', [equipoController::class, 'update'])->name('equipo.update');
/*            Route::get('roles-edit/{user}', [userController::class, 'rolesEdit'])->name('user.roles-edit');
            Route::post('roles-update/{user}', [userController::class, 'rolesUpdate'])->name('user.roles-update');
            Route::get('destroy/{user}', [userController::class, 'destroy'])->name('user.destroy');
 */        });

    });
});
