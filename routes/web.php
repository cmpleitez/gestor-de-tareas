<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        
    //GENERAL
    return view('dashboard');

    //SUPER ADMINISTRADORES
/*     Route::group(['middleware' => ['role:SuperAdmin']], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user');
            Route::get('create', [UserController::class, 'create'])->name('user.create');
            Route::post('store', [UserController::class, 'store'])->name('user.store');
            Route::get('edit/{user}', [UserController::class, 'edit'])->name('user.edit');
            Route::get('roles-edit/{user}', [UserController::class, 'rolesEdit'])->name('user.roles-edit');
            Route::put('update/{user}', [UserController::class, 'update'])->name('user.update');
            Route::post('roles-update/{user}', [UserController::class, 'rolesUpdate'])->name('user.roles-update');
            Route::get('destroy/{user}', [UserController::class, 'destroy'])->name('user.destroy');
            Route::get('activate/{user}', [UserController::class, 'activate'])->name('user.activate');
        });
    });
 */

    })->name('dashboard');
});
