<?php
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RecepcionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\ParametroController;

// DASHBOARD
Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

// VERIFICACION DE CORREO
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

//ENROLADAS
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
    Route::group(['middleware' => 'can:administrar'], function () {
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

        Route::group(['prefix' => 'parametro'], function () { //Parametros
            Route::get('/', [ParametroController::class, 'index'])->name('parametro')->middleware('can:ver');
            Route::get('edit/{parametro}', [ParametroController::class, 'edit'])->name('parametro.edit')->middleware('can:editar');
            Route::put('update/{parametro}', [ParametroController::class, 'update'])->name('parametro.update')->middleware('can:editar');
            Route::post('activate/{parametro}', [ParametroController::class, 'activate'])->name('parametro.activate')->middleware('can:activar');
        });
    });

    //GESTIÓN
    Route::group(['middleware' => ['can:gestionar']], function () {
        Route::group(['prefix' => 'recepcion'], function () {
            Route::get('teams/{solicitud}', [RecepcionController::class, 'equipos'])->name('recepcion.teams')->middleware('can:ver');
            Route::get('operators/{solicitud}', [RecepcionController::class, 'operadores'])->name('recepcion.operators')->middleware('can:ver');
            Route::post('outstanding', [RecepcionController::class, 'nuevasRecibidas'])->name('recepcion.nuevas-recibidas')->middleware('can:autorefrescar');
            Route::post('delegating/{recepcion}/{equipo}', [RecepcionController::class, 'asignar'])->name('recepcion.asignar')->middleware('can:asignar');
            Route::post('next-step/{recepcion}', [RecepcionController::class, 'avanzarEstado'])->name('recepcion.avanzar')->middleware('can:asignar');
            Route::get('tasks/{recepcion_id}', [RecepcionController::class, 'tareas'])->name('recepcion.tareas')->middleware('can:ver-tareas');
            Route::post('purchase-order', [RecepcionController::class, 'ordenCompra'])->name('recepcion.orden-compra')->middleware('can:ver-orden');
            Route::post('fix-request', [RecepcionController::class, 'corregirCarrito'])->name('recepcion.corregir-carrito')->middleware('can:corregir-carrito');
            Route::post('supervise-request', [RecepcionController::class, 'revisarCarrito'])->name('recepcion.revisar-carrito')->middleware('can:revisar');
            Route::get('create-stock', [RecepcionController::class, 'createStock'])->name('recepcion.create-stock')->middleware('can:crear-stock');
            Route::post('store-stock', [RecepcionController::class, 'storeStock'])->name('recepcion.store-stock')->middleware('can:crear-stock');
            Route::post('check-stock', [RecepcionController::class, 'confirmarStock'])->name('recepcion.confirmar-stock')->middleware('can:confirmar-stock');
            Route::post('deplete-stock', [RecepcionController::class, 'descargarStock'])->name('recepcion.descargar-stock')->middleware('can:descargar-stock');
            Route::get('tracking-stocks', [RecepcionController::class, 'historialTransacciones'])->name('recepcion.historial-transacciones')->middleware('can:ver-reportes');
            Route::post('edit-request', [RecepcionController::class, 'editarCarrito'])->name('recepcion.editar-carrito')->middleware('can:editar-carrito');
            Route::post('validate-payment', [RecepcionController::class, 'confirmarPago'])->name('recepcion.confirmar-pago')->middleware('can:confirmar');
            Route::post('fullfill-request', [RecepcionController::class, 'efectuarEntrega'])->name('recepcion.efectuar-entrega')->middleware('can:confirmar');
            Route::post('tracking-read', [RecepcionController::class, 'lecturaTransacciones'])->name('recepcion.lectura-transacciones')->middleware('can:ver-reportes');
        });
    });

    //TIENDA
    Route::group(['middleware' => ['can:tienda']], function () {
        Route::group(['prefix' => 'tienda'], function () {
            Route::get('/', [TiendaController::class, 'index'])->name('tienda')->middleware('can:ver-tienda');
            Route::get('shop.request', [TiendaController::class, 'carritoIndex'])->name('tienda.carrito');
            Route::post('request-send', [TiendaController::class, 'carritoEnviar'])->name('tienda.carrito-enviar');
            Route::get('requests', [TiendaController::class, 'solicitudes'])->name('tienda.solicitudes')->middleware('can:ver-solicitudes');
            Route::get('read-item-stock/{productoId}', [TiendaController::class, 'getStocksProducto'])->name('tienda.get-stocks-producto');
            Route::post('read-kit-products', [TiendaController::class, 'getKitProductos'])->name('tienda.get-kit-productos');
            Route::get('add-item/{orden}', [TiendaController::class, 'agregarOrden'])->name('tienda.agregar-item')->middleware('can:agregar-item');
            Route::post('remove-kit/{orden}', [TiendaController::class, 'retirarOrden'])->name('tienda.retirar-orden')->middleware('can:retirar-orden');
            Route::post('remove-item', [TiendaController::class, 'retirarItem'])->name('tienda.retirar-item')->middleware('can:retirar-item');
            Route::get('kit-quantity', [TiendaController::class, 'kitCantidad'])->name('tienda.kit-cantidad');
            Route::post('read-progress', [TiendaController::class, 'consultarAvance'])->name('tienda.consultar-avance')->middleware('can:autorefrescar');
        });
    });
});
