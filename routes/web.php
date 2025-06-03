<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\equipoController;
use App\Http\Controllers\userController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    //GENERAL
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    //ADMINISTRADORES
    Route::group(['middleware' => ['role:Administradores']], function () {
        Route::group(['prefix' => 'equipo'], function () {
            Route::get('/', [equipoController::class, 'index'])->name('equipo');
            /* Route::get('create', [equipoController::class, 'create'])->name('equipo.create'); */
            /* Route::post('store', [equipoController::class, 'store'])->name('equipo.store'); */
            /* Route::get('edit/{equipo}', [equipoController::class, 'edit'])->name('equipo.edit'); */
            /* Route::get('roles-edit/{equipo}', [equipoController::class, 'rolesEdit'])->name('equipo.roles-edit'); */
            /* Route::put('update/{equipo}', [equipoController::class, 'update'])->name('equipo.update'); */
            /* Route::post('roles-update/{equipo}', [equipoController::class, 'rolesUpdate'])->name('equipo.roles-update'); */
            /* Route::get('destroy/{equipo}', [equipoController::class, 'destroy'])->name('equipo.destroy'); */
            /* Route::get('activate/{equipo}', [equipoController::class, 'activate'])->name('equipo.activate'); */
        });
    });

    //SUPER ADMINISTRADORES
     Route::group(['middleware' => ['role:SuperAdmin']], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user');
            /* Route::get('create', [UserController::class, 'create'])->name('user.create'); */
            /* Route::post('store', [UserController::class, 'store'])->name('user.store'); */
            Route::get('edit/{user}', [UserController::class, 'edit'])->name('user.edit');
            Route::put('update/{user}', [UserController::class, 'update'])->name('user.update');
            Route::get('roles-edit/{user}', [UserController::class, 'rolesEdit'])->name('user.roles-edit');
            Route::post('roles-update/{user}', [UserController::class, 'rolesUpdate'])->name('user.roles-update');
            /* Route::get('destroy/{user}', [UserController::class, 'destroy'])->name('user.destroy'); */
            /* Route::get('activate/{user}', [UserController::class, 'activate'])->name('user.activate'); */
        });
    });
 

});
