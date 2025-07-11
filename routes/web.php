<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\EquipoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\RecepcionController;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/* Route::group(['prefix' => 'user'], function () { //Usuarios
    Route::get('create', [userController::class, 'create'])->name('user.create');
    Route::post('store', [userController::class, 'store'])->name('user.store');
}); */

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
            Route::get('edit/{user}', [userController::class, 'edit'])->name('user.edit');
            Route::put('update/{user}', [userController::class, 'update'])->name('user.update');
            Route::get('roles-edit/{user}', [userController::class, 'rolesEdit'])->name('user.roles-edit');
            Route::post('roles-update/{user}', [userController::class, 'rolesUpdate'])->name('user.roles-update');
            Route::get('equipos-edit/{user}', [userController::class, 'equiposEdit'])->name('user.equipos-edit');
            Route::post('equipos-update/{user}', [userController::class, 'equiposUpdate'])->name('user.equipos-update');
            Route::get('solicitudes-edit/{user}', [userController::class, 'solicitudesEdit'])->name('user.solicitudes-edit');
            Route::post('solicitudes-update/{user}', [userController::class, 'solicitudesUpdate'])->name('user.solicitudes-update');
            Route::get('destroy/{user}', [userController::class, 'destroy'])->name('user.destroy');
            Route::post('activate/{user}', [userController::class, 'activate'])->name('user.activate');
        });
        
        Route::group(['prefix' => 'equipo'], function () { //Equipos
            Route::get('/', [equipoController::class, 'index'])->name('equipo');
            Route::get('create', [equipoController::class, 'create'])->name('equipo.create');
            Route::post('store', [equipoController::class, 'store'])->name('equipo.store');
            Route::get('edit/{equipo}', [equipoController::class, 'edit'])->name('equipo.edit');
            Route::put('update/{equipo}', [equipoController::class, 'update'])->name('equipo.update');
            Route::get('destroy/{equipo}', [equipoController::class, 'destroy'])->name('equipo.destroy');
            Route::post('activate/{equipo}', [equipoController::class, 'activate'])->name('equipo.activate');
        });

        Route::group(['prefix' => 'tarea'], function () { //Tareas
            Route::get('/', [tareaController::class, 'index'])->name('tarea');
            Route::get('create', [tareaController::class, 'create'])->name('tarea.create');
            Route::post('store', [tareaController::class, 'store'])->name('tarea.store');
            Route::get('edit/{tarea}', [tareaController::class, 'edit'])->name('tarea.edit');
            Route::put('update/{tarea}', [tareaController::class, 'update'])->name('tarea.update');
            Route::get('destroy/{tarea}', [tareaController::class, 'destroy'])->name('tarea.destroy');
            Route::post('activate/{tarea}', [tareaController::class, 'activate'])->name('tarea.activate');
        });

        Route::group(['prefix' => 'solicitud'], function () { //Solicitudes
            Route::get('/', [solicitudController::class, 'index'])->name('solicitud');
            Route::get('create', [solicitudController::class, 'create'])->name('solicitud.create');
            Route::post('store', [solicitudController::class, 'store'])->name('solicitud.store');
            Route::get('edit/{solicitud}', [solicitudController::class, 'edit'])->name('solicitud.edit');
            Route::put('update/{solicitud}', [solicitudController::class, 'update'])->name('solicitud.update');
            Route::get('asignar-tareas/{solicitud}', [solicitudController::class, 'asignarTareas'])->name('solicitud.asignar-tareas');
            Route::put('actualizar-tareas/{solicitud}', [solicitudController::class, 'actualizarTareas'])->name('solicitud.actualizar-tareas');
            Route::get('destroy/{solicitud}', [solicitudController::class, 'destroy'])->name('solicitud.destroy');
            Route::post('activate/{solicitud}', [solicitudController::class, 'activate'])->name('solicitud.activate');
        });
    });

    //BENEFICIARIOS
    Route::group(['middleware' => ['role:Recepcionista|Supervisor|Gestor']], function () {
        Route::group(['prefix' => 'recepcion'], function () {
            Route::get('/', [RecepcionController::class, 'index'])->name('recepcion');
            Route::get('recepciones', [recepcionController::class, 'recepciones'])->name('recepcion.solicitudes');
            Route::get('areas/{solicitud}', [recepcionController::class, 'areas'])->name('recepcion.areas');
            Route::get('equipos/{solicitud}', [recepcionController::class, 'equipos'])->name('recepcion.equipos');
            Route::get('operadores/{solicitud}', [recepcionController::class, 'operadores'])->name('recepcion.operadores');
            Route::post('activate/{recepcion}', [RecepcionController::class, 'activate'])->name('recepcion.activate');
            Route::post('derivar/{recepcion_id}/{area_id}', [RecepcionController::class, 'derivar'])->name('recepcion.derivar');
            Route::post('asignar/{recepcion_id}/{equipo_id}', [RecepcionController::class, 'asignar'])->name('recepcion.asignar');
            Route::post('delegar/{recepcion_id}/{user_id}', [RecepcionController::class, 'delegar'])->name('recepcion.delegar');
            Route::get('mis-tareas', [RecepcionController::class, 'misTareas'])->name('recepcion.mis-tareas');
        });
    });

    //BENEFICIARIOS
    Route::group(['middleware' => ['role:Beneficiario']], function () {
        Route::group(['prefix' => 'recepcion'], function () {
            Route::get('create', [RecepcionController::class, 'create'])->name('recepcion.create');
            Route::post('store', [RecepcionController::class, 'store'])->name('recepcion.store');
        });
    });
});
