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
    Route::group(['middleware' => ['role:admin|superadmin']], function () {
        Route::group(['prefix' => 'user'], function () { //Usuarios
            Route::get('/', [userController::class, 'index'])->name('user');
            Route::get('edit/{user}', [userController::class, 'edit'])->name('user.edit');
            Route::put('update/{user}', [userController::class, 'update'])->name('user.update');
            Route::get('roles-edit/{user}', [userController::class, 'rolesEdit'])->name('user.roles-edit');
            Route::post('roles-update/{user}', [userController::class, 'rolesUpdate'])->name('user.roles-update');
            Route::get('equipos-edit/{user}', [userController::class, 'equiposEdit'])->name('user.equipos-edit');
            Route::post('equipos-update/{user}', [userController::class, 'equiposUpdate'])->name('user.equipos-update');
            Route::get('tareas-edit/{user}', [userController::class, 'tareasEdit'])->name('user.tareas-edit');
            Route::post('tareas-update/{user}', [userController::class, 'tareasUpdate'])->name('user.tareas-update');
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

        Route::group(['prefix' => 'marca'], function () { //Marcas
            Route::get('/', [marcaController::class, 'index'])->name('marca');
            Route::get('create', [marcaController::class, 'create'])->name('marca.create');
            Route::post('store', [marcaController::class, 'store'])->name('marca.store');
            Route::get('edit/{marca}', [marcaController::class, 'edit'])->name('marca.edit');
            Route::put('update/{marca}', [marcaController::class, 'update'])->name('marca.update');
            Route::get('destroy/{marca}', [marcaController::class, 'destroy'])->name('marca.destroy');
            Route::post('activate/{marca}', [marcaController::class, 'activate'])->name('marca.activate');
        });

        Route::group(['prefix' => 'modelo'], function () { //Modelos
            Route::get('/', [modeloController::class, 'index'])->name('modelo');
            Route::get('create', [modeloController::class, 'create'])->name('modelo.create');
            Route::post('store', [modeloController::class, 'store'])->name('modelo.store');
            Route::get('edit/{modelo}', [modeloController::class, 'edit'])->name('modelo.edit');
            Route::put('update/{modelo}', [modeloController::class, 'update'])->name('modelo.update');
            Route::get('destroy/{modelo}', [modeloController::class, 'destroy'])->name('modelo.destroy');
            Route::post('activate/{modelo}', [modeloController::class, 'activate'])->name('modelo.activate');
        });

        Route::group(['prefix' => 'tipo'], function () { //Tipos
            Route::get('/', [TipoController::class, 'index'])->name('tipo');
            Route::get('create', [TipoController::class, 'create'])->name('tipo.create');
            Route::post('store', [TipoController::class, 'store'])->name('tipo.store');
            Route::get('edit/{tipo}', [TipoController::class, 'edit'])->name('tipo.edit');
            Route::put('update/{tipo}', [TipoController::class, 'update'])->name('tipo.update');
            Route::get('destroy/{tipo}', [TipoController::class, 'destroy'])->name('tipo.destroy');
            Route::post('activate/{tipo}', [TipoController::class, 'activate'])->name('tipo.activate');
        });

        Route::group(['prefix' => 'producto'], function () { //Productos
            Route::get('/', [ProductoController::class, 'index'])->name('producto');
            Route::get('create', [ProductoController::class, 'create'])->name('producto.create');
            Route::post('store', [ProductoController::class, 'store'])->name('producto.store');
            Route::get('edit/{producto}', [ProductoController::class, 'edit'])->name('producto.edit');
            Route::put('update/{producto}', [ProductoController::class, 'update'])->name('producto.update');
            Route::get('destroy/{producto}', [ProductoController::class, 'destroy'])->name('producto.destroy');
            Route::post('activate/{producto}', [ProductoController::class, 'activate'])->name('producto.activate');
        });

        Route::group(['prefix' => 'kit'], function () { //Kits
            Route::get('/', [KitController::class, 'index'])->name('kit');
            Route::get('create', [KitController::class, 'create'])->name('kit.create');
            Route::post('store', [KitController::class, 'store'])->name('kit.store');
            Route::get('edit/{kit}', [KitController::class, 'edit'])->name('kit.edit');
            Route::put('update/{kit}', [KitController::class, 'update'])->name('kit.update');
            Route::get('asignar-productos/{kit}', [KitController::class, 'asignarProductos'])->name('kit.asignar-productos');
            Route::put('sincronizar-productos/{kit}', [KitController::class, 'sincronizarProductos'])->name('kit.sincronizar-productos');
            Route::put('store-equivalente/{kit}', [KitController::class, 'storeEquivalente'])->name('kit.store-equivalente');
            Route::get('destroy-equivalente/{kit_producto_id}/{producto_id}/{kit_id}', [KitController::class, 'destroyEquivalente'])->name('kit.destroy-equivalente');
            Route::get('destroy/{kit}', [KitController::class, 'destroy'])->name('kit.destroy');
            Route::post('activate/{kit}', [KitController::class, 'activate'])->name('kit.activate');
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

    //GESTIÓN
    Route::get('parametros', [RecepcionController::class, 'parametros'])->name('recepcion.parametros');
    Route::get('parametros/edit/{parametro}', [RecepcionController::class, 'parametrosEdit'])->name('recepcion.parametros-edit');
    Route::put('parametros/update/{parametro}', [RecepcionController::class, 'parametrosUpdate'])->name('recepcion.parametros-update');
    Route::post('parametros/activate/{parametro}', [RecepcionController::class, 'parametrosActivate'])->name('recepcion.parametros-activate');
    Route::get('solicitudes', [RecepcionController::class, 'solicitudes'])->name('recepcion.solicitudes');
    Route::get('equipos/{solicitud}', [RecepcionController::class, 'equipos'])->name('recepcion.equipos');
    Route::get('operadores/{solicitud}', [RecepcionController::class, 'operadores'])->name('recepcion.operadores');
    Route::post('avance-tablero', [RecepcionController::class, 'consultarAvance'])->name('recepcion.consultar-avance');
    Route::post('nuevas-recibidas', [RecepcionController::class, 'nuevasRecibidas'])->name('recepcion.nuevas-recibidas');
    Route::group(['middleware' => ['role:receptor|operador']], function () {
        Route::group(['prefix' => 'recepcion'], function () {
            Route::post('asignar/{recepcion}/{equipo}', [RecepcionController::class, 'asignar'])->name('recepcion.asignar');
            Route::get('tareas/{recepcion_id}', [RecepcionController::class, 'tareas'])->name('recepcion.tareas');
            
            
            Route::post('confirmar-fisico', [RecepcionController::class, 'confirmarFisico'])->name('recepcion.confirmar-fisico');
            Route::post('efectuar-pago', [RecepcionController::class, 'efectuarPago'])->name('recepcion.efectuar-pago');
            Route::post('descargar-stock', [RecepcionController::class, 'descargarStock'])->name('recepcion.descargar-stock');
            Route::post('efectuar-entrega', [RecepcionController::class, 'efectuarEntrega'])->name('recepcion.efectuar-entrega');



        });
    });

    //TIENDA
    Route::group(['middleware' => ['role:cliente|receptor']], function () {
        Route::group(['prefix' => 'tienda'], function () {

            Route::get('/', [TiendaController::class, 'index'])->name('tienda');
            Route::get('carrito', [TiendaController::class, 'carritoIndex'])->name('tienda.carrito');
            Route::post('carrito-editar', [TiendaController::class, 'carritoEditar'])->name('tienda.carrito-editar');
            Route::post('carrito-enviar', [TiendaController::class, 'carritoEnviar'])->name('tienda.carrito-enviar');
            Route::get('stock', [TiendaController::class, 'createStock'])->name('tienda.create-stock');
            Route::post('stock', [TiendaController::class, 'storeStock'])->name('tienda.store-stock');
            Route::get('get-stocks-producto/{productoId}', [TiendaController::class, 'getStocksProducto'])->name('tienda.get-stocks-producto');
            Route::post('get-kit-productos', [TiendaController::class, 'getKitProductos'])->name('tienda.get-kit-productos');
            Route::get('agregar-orden/{orden}', [TiendaController::class, 'agregarOrden'])->name('tienda.agregar-orden');
            Route::post('retirar-orden/{orden}', [TiendaController::class, 'retirarOrden'])->name('tienda.retirar-orden');
            Route::post('retirar-item', [TiendaController::class, 'retirarItem'])->name('tienda.retirar-item');
            Route::get('kit-cantidad', [TiendaController::class, 'kitCantidad'])->name('tienda.kit-cantidad');
            

        });
    });
});
