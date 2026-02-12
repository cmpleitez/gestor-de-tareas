<?php
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\KitController;

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

//Rutas protegidas que requieren verificación de correo
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    //DASHBOARD GENERAL
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //ADMINISTRACIÓN
    Route::group(['middleware' => ['can:administrar']], function () {
        Route::group(['prefix' => 'user'], function () { //Usuarios
            Route::get('/', [userController::class, 'index'])->name('user')->middleware('can:ver');
            Route::get('edit/{user}', [userController::class, 'edit'])->name('user.edit')->middleware('can:editar');
            Route::put('update/{user}', [userController::class, 'update'])->name('user.update')->middleware('can:editar');
            Route::get('roles-edit/{user}', [userController::class, 'rolesEdit'])->name('user.roles-edit')->middleware('can:editar');
            Route::post('roles-update/{user}', [userController::class, 'rolesUpdate'])->name('user.roles-update')->middleware('can:editar');
            Route::get('equipos-edit/{user}', [userController::class, 'equiposEdit'])->name('user.equipos-edit')->middleware('can:editar');
            Route::post('equipos-update/{user}', [userController::class, 'equiposUpdate'])->name('user.equipos-update')->middleware('can:editar');
            Route::get('tareas-edit/{user}', [userController::class, 'tareasEdit'])->name('user.tareas-edit')->middleware('can:editar');
            Route::post('tareas-update/{user}', [userController::class, 'tareasUpdate'])->name('user.tareas-update')->middleware('can:editar');
            Route::get('destroy/{user}', [userController::class, 'destroy'])->name('user.destroy')->middleware('can:eliminar');
            Route::post('activate/{user}', [userController::class, 'activate'])->name('user.activate')->middleware('can:activar');
        });

        Route::group(['prefix' => 'equipo'], function () { //Equipos
            Route::get('/', [equipoController::class, 'index'])->name('equipo')->middleware('can:ver');
            Route::get('create', [equipoController::class, 'create'])->name('equipo.create')->middleware('can:crear');
            Route::post('store', [equipoController::class, 'store'])->name('equipo.store')->middleware('can:crear');
            Route::get('edit/{equipo}', [equipoController::class, 'edit'])->name('equipo.edit')->middleware('can:editar');
            Route::put('update/{equipo}', [equipoController::class, 'update'])->name('equipo.update')->middleware('can:editar');
            Route::get('destroy/{equipo}', [equipoController::class, 'destroy'])->name('equipo.destroy')->middleware('can:eliminar');
            Route::post('activate/{equipo}', [equipoController::class, 'activate'])->name('equipo.activate')->middleware('can:activar');
        });
        
        Route::group(['prefix' => 'tarea'], function () { //Tareas
            Route::get('/', [tareaController::class, 'index'])->name('tarea')->middleware('can:ver');
            Route::get('create', [tareaController::class, 'create'])->name('tarea.create')->middleware('can:crear');
            Route::post('store', [tareaController::class, 'store'])->name('tarea.store')->middleware('can:crear');
            Route::get('edit/{tarea}', [tareaController::class, 'edit'])->name('tarea.edit')->middleware('can:editar');
            Route::put('update/{tarea}', [tareaController::class, 'update'])->name('tarea.update')->middleware('can:editar');
            Route::get('destroy/{tarea}', [tareaController::class, 'destroy'])->name('tarea.destroy')->middleware('can:eliminar');
            Route::post('activate/{tarea}', [tareaController::class, 'activate'])->name('tarea.activate')->middleware('can:activar');
        });

        Route::group(['prefix' => 'marca'], function () { //Marcas
            Route::get('/', [marcaController::class, 'index'])->name('marca')->middleware('can:ver');
            Route::get('create', [marcaController::class, 'create'])->name('marca.create')->middleware('can:crear');
            Route::post('store', [marcaController::class, 'store'])->name('marca.store')->middleware('can:crear');
            Route::get('edit/{marca}', [marcaController::class, 'edit'])->name('marca.edit')->middleware('can:editar');
            Route::put('update/{marca}', [marcaController::class, 'update'])->name('marca.update')->middleware('can:editar');
            Route::get('destroy/{marca}', [marcaController::class, 'destroy'])->name('marca.destroy')->middleware('can:eliminar');
            Route::post('activate/{marca}', [marcaController::class, 'activate'])->name('marca.activate')->middleware('can:activar');
        });

        Route::group(['prefix' => 'modelo'], function () { //Modelos
            Route::get('/', [modeloController::class, 'index'])->name('modelo')->middleware('can:ver');
            Route::get('create', [modeloController::class, 'create'])->name('modelo.create')->middleware('can:crear');
            Route::post('store', [modeloController::class, 'store'])->name('modelo.store')->middleware('can:crear');
            Route::get('edit/{modelo}', [modeloController::class, 'edit'])->name('modelo.edit')->middleware('can:editar');
            Route::put('update/{modelo}', [modeloController::class, 'update'])->name('modelo.update')->middleware('can:editar');
            Route::get('destroy/{modelo}', [modeloController::class, 'destroy'])->name('modelo.destroy')->middleware('can:eliminar');
            Route::post('activate/{modelo}', [modeloController::class, 'activate'])->name('modelo.activate')->middleware('can:activar');
        });

        Route::group(['prefix' => 'tipo'], function () { //Tipos
            Route::get('/', [TipoController::class, 'index'])->name('tipo')->middleware('can:ver');
            Route::get('create', [TipoController::class, 'create'])->name('tipo.create')->middleware('can:crear');
            Route::post('store', [TipoController::class, 'store'])->name('tipo.store')->middleware('can:crear');
            Route::get('edit/{tipo}', [TipoController::class, 'edit'])->name('tipo.edit')->middleware('can:editar');
            Route::put('update/{tipo}', [TipoController::class, 'update'])->name('tipo.update')->middleware('can:editar');
            Route::get('destroy/{tipo}', [TipoController::class, 'destroy'])->name('tipo.destroy')->middleware('can:eliminar');
            Route::post('activate/{tipo}', [TipoController::class, 'activate'])->name('tipo.activate')->middleware('can:activar');
        });

        Route::group(['prefix' => 'producto'], function () { //Productos
            Route::get('/', [ProductoController::class, 'index'])->name('producto')->middleware('can:ver');
            Route::get('create', [ProductoController::class, 'create'])->name('producto.create')->middleware('can:crear');
            Route::post('store', [ProductoController::class, 'store'])->name('producto.store')->middleware('can:crear');
            Route::get('edit/{producto}', [ProductoController::class, 'edit'])->name('producto.edit')->middleware('can:editar');
            Route::put('update/{producto}', [ProductoController::class, 'update'])->name('producto.update')->middleware('can:editar');
            Route::get('destroy/{producto}', [ProductoController::class, 'destroy'])->name('producto.destroy')->middleware('can:eliminar');
            Route::post('activate/{producto}', [ProductoController::class, 'activate'])->name('producto.activate')->middleware('can:activar');
        });

        Route::group(['prefix' => 'kit'], function () { //Kits
            Route::get('/', [KitController::class, 'index'])->name('kit')->middleware('can:ver');
            Route::get('create', [KitController::class, 'create'])->name('kit.create')->middleware('can:crear');
            Route::post('store', [KitController::class, 'store'])->name('kit.store')->middleware('can:crear');
            Route::get('edit/{kit}', [KitController::class, 'edit'])->name('kit.edit')->middleware('can:editar');
            Route::put('update/{kit}', [KitController::class, 'update'])->name('kit.update')->middleware('can:editar');
            Route::get('asignar-productos/{kit}', [KitController::class, 'asignarProductos'])->name('kit.asignar-productos')->middleware('can:editar');
            Route::put('sincronizar-productos/{kit}', [KitController::class, 'sincronizarProductos'])->name('kit.sincronizar-productos')->middleware('can:editar');
            Route::put('store-equivalente/{kit}', [KitController::class, 'storeEquivalente'])->name('kit.store-equivalente')->middleware('can:editar');
            Route::get('destroy-equivalente/{kit_producto_id}/{producto_id}/{kit_id}', [KitController::class, 'destroyEquivalente'])->name('kit.destroy-equivalente')->middleware('can:eliminar');
            Route::get('destroy/{kit}', [KitController::class, 'destroy'])->name('kit.destroy')->middleware('can:eliminar');
            Route::post('activate/{kit}', [KitController::class, 'activate'])->name('kit.activate')->middleware('can:activar');
        });
    });

    //GESTIÓN
    Route::get('parametros', [RecepcionController::class, 'parametros'])->name('recepcion.parametros')->middleware('can:ver');
    Route::get('parametros/edit/{parametro}', [RecepcionController::class, 'parametrosEdit'])->name('recepcion.parametros-edit')->middleware('can:editar');
    Route::put('parametros/update/{parametro}', [RecepcionController::class, 'parametrosUpdate'])->name('recepcion.parametros-update')->middleware('can:editar');
    Route::post('parametros/activate/{parametro}', [RecepcionController::class, 'parametrosActivate'])->name('recepcion.parametros-activate')->middleware('can:activar');
    Route::get('solicitudes', [RecepcionController::class, 'solicitudes'])->name('recepcion.solicitudes')->middleware('can:ver');
    Route::get('equipos/{solicitud}', [RecepcionController::class, 'equipos'])->name('recepcion.equipos')->middleware('can:ver');
    Route::get('operadores/{solicitud}', [RecepcionController::class, 'operadores'])->name('recepcion.operadores')->middleware('can:ver');
    Route::post('avance-tablero', [RecepcionController::class, 'consultarAvance'])->name('recepcion.consultar-avance')->middleware('can:editar');
    Route::post('nuevas-recibidas', [RecepcionController::class, 'nuevasRecibidas'])->name('recepcion.nuevas-recibidas')->middleware('can:editar');
    Route::group(['middleware' => ['can:gestionar']], function () {
        Route::group(['prefix' => 'solicitud'], function () { //Solicitudes
            Route::get('/', [solicitudController::class, 'index'])->name('solicitud')->middleware('can:ver');
            Route::get('create', [solicitudController::class, 'create'])->name('solicitud.create')->middleware('can:crear');
            Route::post('store', [solicitudController::class, 'store'])->name('solicitud.store')->middleware('can:crear');
            Route::get('edit/{solicitud}', [solicitudController::class, 'edit'])->name('solicitud.edit')->middleware('can:editar');
            Route::put('update/{solicitud}', [solicitudController::class, 'update'])->name('solicitud.update')->middleware('can:editar');
            Route::get('asignar-tareas/{solicitud}', [solicitudController::class, 'asignarTareas'])->name('solicitud.asignar-tareas')->middleware('can:asignar');
            Route::put('actualizar-tareas/{solicitud}', [solicitudController::class, 'actualizarTareas'])->name('solicitud.actualizar-tareas')->middleware('can:editar');
            Route::get('destroy/{solicitud}', [solicitudController::class, 'destroy'])->name('solicitud.destroy')->middleware('can:eliminar');
            Route::post('activate/{solicitud}', [solicitudController::class, 'activate'])->name('solicitud.activate')->middleware('can:activar');
        });
        Route::group(['prefix' => 'recepcion'], function () {
            Route::post('asignar/{recepcion}/{equipo}', [RecepcionController::class, 'asignar'])->name('recepcion.asignar')->middleware('can:asignar');
            Route::get('tareas/{recepcion_id}', [RecepcionController::class, 'tareas'])->name('recepcion.tareas');
            Route::post('corregir-orden', [RecepcionController::class, 'corregirOrden'])->name('recepcion.corregir-orden')->middleware('can:editar');
            Route::post('revisar-orden', [RecepcionController::class, 'revisarOrden'])->name('recepcion.revisar-orden')->middleware('can:editar');
            Route::post('revisar-stock', [RecepcionController::class, 'revisarStock'])->name('recepcion.revisar-stock')->middleware('can:editar');
            Route::post('confirmar-pago', [RecepcionController::class, 'efectuarPago'])->name('recepcion.confirmar-pago')->middleware('can:autorizar');
            Route::post('descargar-stock', [RecepcionController::class, 'descargarStock'])->name('recepcion.descargar-stock')->middleware('can:editar');
            Route::post('efectuar-entrega', [RecepcionController::class, 'efectuarEntrega'])->name('recepcion.efectuar-entrega')->middleware('can:autorizar');
        });
    });

    //TIENDA
    Route::group(['middleware' => ['can:tienda']], function () {
        Route::group(['prefix' => 'tienda'], function () {
            Route::get('/', [TiendaController::class, 'index'])->name('tienda')->middleware('can:ver');
            Route::get('carrito', [TiendaController::class, 'carritoIndex'])->name('tienda.carrito')->middleware('can:ver');
            Route::post('carrito-editar', [TiendaController::class, 'carritoEditar'])->name('tienda.carrito-editar')->middleware('can:editar');
            Route::post('carrito-enviar', [TiendaController::class, 'carritoEnviar'])->name('tienda.carrito-enviar')->middleware('can:crear');
            Route::get('stock', [TiendaController::class, 'createStock'])->name('tienda.create-stock')->middleware('can:crear');
            Route::post('stock', [TiendaController::class, 'storeStock'])->name('tienda.store-stock')->middleware('can:editar');
            Route::get('get-stocks-producto/{productoId}', [TiendaController::class, 'getStocksProducto'])->name('tienda.get-stocks-producto')->middleware('can:ver');
            Route::post('get-kit-productos', [TiendaController::class, 'getKitProductos'])->name('tienda.get-kit-productos')->middleware('can:ver');
            Route::get('agregar-orden/{orden}', [TiendaController::class, 'agregarOrden'])->name('tienda.agregar-orden')->middleware('can:crear');
            Route::post('retirar-orden/{orden}', [TiendaController::class, 'retirarOrden'])->name('tienda.retirar-orden')->middleware('can:editar');
            Route::post('retirar-item', [TiendaController::class, 'retirarItem'])->name('tienda.retirar-item')->middleware('can:editar');
            Route::get('kit-cantidad', [TiendaController::class, 'kitCantidad'])->name('tienda.kit-cantidad')->middleware('can:ver');
        });
    });
});
